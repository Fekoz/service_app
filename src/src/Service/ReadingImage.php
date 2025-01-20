<?php


namespace App\Service;


use App\Util\Constant;
use Doctrine\ORM\EntityManagerInterface;
use Imagine\Image\Palette\Color\ColorInterface;
use Imagine\Image\Point;
use Phpml\Clustering\DBSCAN;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Imagine\Gd\Imagine;
use Imagine\Image\Box;
use Phpml\Clustering\KMeans;
use Imagine\Image\ImagineInterface;
use Phpml\Math\Statistic;

class ReadingImage
{
    /**
     * @var object
     */
    private $param;

    /**
     * @var EntityManagerInterface
     */
    private $entity;

    public function __construct(EntityManagerInterface $entity, ParameterBagInterface $param)
    {
        $this->entity = $entity;
        $this->param = (object) $param->get(Constant::CONFIG_NAME[__CLASS__]);
    }

    private function findToCurrent(array $colorsCount, array $colorRanges): array
    {
        $matchingColors = [];
        foreach ($colorsCount as $color => $count) {
            $closestColor = null;
            $closestDistance = PHP_INT_MAX;

            if(count($matchingColors) > 10) {
                break;
            }

            // Преобразуем цвет из строки в массив чисел
            $color = explode(',', $color);
            $color = array_map('intval', $color);

            // Вычисляем яркость цвета (сумма значений R, G, B)
            $brightness = array_sum($color);

            // Пропускаем цвета с низкой яркостью (например, менее 100)
            if ($brightness < 100) {
                continue;
            }

            // Находим ближайший цвет из вашего массива
            foreach ($colorRanges as $rangeColor => $range) {
                // Вычисляем евклидово расстояние между текущим топ цветом и цветом из массива
                $distance = sqrt(
                    pow($range['min'][0] - $color[0], 2) +
                    pow($range['min'][1] - $color[1], 2) +
                    pow($range['min'][2] - $color[2], 2)
                );

                // Обновляем ближайший цвет, если расстояние меньше
                if ($distance < $closestDistance) {
                    $closestColor = $rangeColor;
                    $closestDistance = $distance;
                }
            }

            // Добавляем соответствие ближайшего цвета в массив
            if(!isset($matchingColors[$closestColor])) {
                $matchingColors[$closestColor] = [$color[0],$color[1],$color[2]];
            }
        }

        return $matchingColors;
    }

    private function findToDynamic(array $colorsCount, array $colorRanges): array
    {
        // Находим динамический топ цветов
        $dynamicTopColors = [];
        $previousCount = PHP_INT_MAX; // Изначально задаем большое число, чтобы начать со всех цветов
        $countColors = count($colorsCount);
        $remainingColors = $countColors;
        $matchingColors = [];

        foreach ($colorsCount as $color => $count) {
            // Добавляем текущий цвет в топ, если его количество повторений больше, чем у предыдущего цвета
            if ($count < $previousCount) {
                $dynamicTopColors[] = $color;
                $remainingColors--;
                // Если количество элементов в топе достигло максимального значения или
                // количество повторений текущего цвета меньше или равно количеству повторений следующего цвета,
                // либо достигнуто минимальное количество цветов в топе, заканчиваем формирование топа
                if (count($dynamicTopColors) >= 15 || ($remainingColors > 15 && count($dynamicTopColors) == $remainingColors)) {
                    break;
                }
            } else {
                // Если текущий цвет имеет такое же количество повторений, как предыдущий, добавим его в топ
                // это позволит увеличить разнообразие цветов в топе
                $dynamicTopColors[] = $color;
            }
            $previousCount = $count;
        }

        // Создаем массив для хранения соответствий топ цветов цветам из вашего массива
        $matchingDynamicTopColors = [];

        foreach ($dynamicTopColors as $color) {
            $closestColor = null;
            $closestDistance = PHP_INT_MAX;

            // Преобразуем цвет из строки в массив чисел
            $color = explode(',', $color);
            $color = array_map('intval', $color);

            // Находим ближайший цвет из вашего массива
            foreach ($colorRanges as $rangeColor => $range) {
                // Вычисляем евклидово расстояние между текущим цветом и цветом из массива
                $distance = sqrt(
                    pow($range['min'][0] - $color[0], 2) +
                    pow($range['min'][1] - $color[1], 2) +
                    pow($range['min'][2] - $color[2], 2)
                );

                // Обновляем ближайший цвет, если расстояние меньше
                if ($distance < $closestDistance) {
                    $closestColor = $rangeColor;
                    $closestDistance = $distance;
                }
            }

            // Добавляем соответствие ближайшего цвета в массив
            $matchingColors[$closestColor] = [$color[0],$color[1],$color[2]];
        }

        return $matchingColors;
    }

    private function findToDynamicParsed($image, array $colorRanges): array {// Получаем пиксели изображения
        $pixels = $this->getPixels($image);
        $dbscan = new KMeans(4);
        // Выполняем кластеризацию
        $clusters = $dbscan->cluster($pixels);

        // Извлекаем основные цвета из кластеров
        $colors = [];
        foreach ($clusters as $cluster) {
            // Подсчитываем частоту каждого цвета в кластере
            $colorCounts = [];
            foreach ($cluster as $color) {
                // Преобразуем цвет в строку, чтобы использовать его в качестве ключа в массиве $colorCounts
                $colorString = implode(',', $color);
                // Увеличиваем счетчик для данного цвета
                if (!isset($colorCounts[$colorString])) {
                    $colorCounts[$colorString] = 0;
                }
                $colorCounts[$colorString]++;
            }

            // Сортируем цвета по убыванию частоты
            arsort($colorCounts);

            // Выбираем топ N цветов
            $topColors = array_slice($colorCounts, 0, 5, true);

            // Добавляем выбранные цвета в массив
            foreach ($topColors as $color => $count) {
                $colors[] = explode(',', $color);
            }
        }

        $matchingColors = [];
        $closestColor = null;
        $closestDistance = PHP_INT_MAX;
        foreach ($colors as $color) {
            // Находим ближайший цвет из вашего массива
            foreach ($colorRanges as $rangeColor => $range) {
                // Вычисляем евклидово расстояние между текущим топ цветом и цветом из массива
                $distance = sqrt(
                    pow($range['min'][0] - $color[0], 2) +
                    pow($range['min'][1] - $color[1], 2) +
                    pow($range['min'][2] - $color[2], 2)
                );

                // Обновляем ближайший цвет, если расстояние меньше
                if ($distance < $closestDistance) {
                    $closestColor = $rangeColor;
                    $closestDistance = $distance;
                }
            }

            // Добавляем соответствие ближайшего цвета в массив
            if(!isset($matchingColors[$closestColor])) {
                $matchingColors[$closestColor] = [$color[0],$color[1],$color[2]];
            }
        }

        return $matchingColors;
    }

    private function getPixels($image): array
    {
        $pixels = [];

        // Получаем размеры изображения
        $imageSize = $image->getSize();
        $imageWidth = $imageSize->getWidth();
        $imageHeight = $imageSize->getHeight();

        // Проходим по каждому пикселю изображения и добавляем его в массив пикселей
        for ($x = 0; $x < $imageWidth; $x++) {
            for ($y = 0; $y < $imageHeight; $y++) {
                // Получаем цвет пикселя
                $color = $image->getColorAt(new Point($x, $y));

                // Получаем RGB значения цвета
                $red = $color->getValue(ColorInterface::COLOR_RED);
                $green = $color->getValue(ColorInterface::COLOR_GREEN);
                $blue = $color->getValue(ColorInterface::COLOR_BLUE);

                // Добавляем пиксель в массив
                $pixels[] = [$red, $green, $blue];
            }
        }

        return $pixels;
    }


    public function uploadImage(string $imageName): void
    {
        // Путь к вашему изображению
        $imagePath = $this->param->kernel_dir . "/" . $imageName;

        // Создаем экземпляр Imagine
        $imagine = new Imagine();

        // Открываем изображение
        $image = $imagine->open($imagePath);

        $colorRanges = [
            'белый' => ['min' => [245, 245, 245], 'max' => [255, 255, 255]],
            'бежевый' => ['min' => [245, 245, 220], 'max' => [247, 247, 222]],
            'черный' => ['min' => [0, 0, 0], 'max' => [10, 10, 10]],
            'коричневый' => ['min' => [139, 69, 19], 'max' => [205, 133, 63]],
            'серый' => ['min' => [128, 128, 128], 'max' => [150, 150, 150]],
            'серый металлик' => ['min' => [128, 128, 128], 'max' => [150, 150, 150]],
            'желтый' => ['min' => [255, 255, 0], 'max' => [255, 255, 50]],
            'красный' => ['min' => [205, 0, 0], 'max' => [255, 50, 50]],
            'розовый' => ['min' => [255, 182, 193], 'max' => [255, 192, 203]],
            'синий' => ['min' => [0, 0, 205], 'max' => [50, 50, 255]],
            'золотой' => ['min' => [255, 215, 0], 'max' => [255, 215, 50]],
            'зеленый' => ['min' => [0, 128, 0], 'max' => [50, 205, 50]],
            'голубой' => ['min' => [0, 191, 255], 'max' => [50, 205, 255]],
            'оранжевый' => ['min' => [255, 69, 0], 'max' => [255, 125, 50]],
            'фиолетовый' => ['min' => [128, 0, 128], 'max' => [178, 58, 238]],
            'бронза' => ['min' => [140, 120, 83], 'max' => [205, 179, 139]],
            'сиреневый' => ['min' => [200, 162, 200], 'max' => [226, 160, 210]],
            'светло-зеленый' => ['min' => [144, 238, 144], 'max' => [173, 255, 47]],
            'бордовый' => ['min' => [128, 0, 32], 'max' => [165, 42, 42]],
            'светло-коричневый' => ['min' => [205, 133, 63], 'max' => [244, 164, 96]],
            'темно-синий' => ['min' => [0, 0, 139], 'max' => [25, 25, 112]],
            'светло-бежевый' => ['min' => [245, 245, 220], 'max' => [255, 250, 205]],
            'светло-серый' => ['min' => [211, 211, 211], 'max' => [220, 220, 220]],
            'бирюзовый' => ['min' => [0, 206, 209], 'max' => [70, 130, 180]],
            'светло-розовый' => ['min' => [255, 182, 193], 'max' => [255, 192, 203]],
            'слоновая кость' => ['min' => [251, 236, 199], 'max' => [250, 240, 230]],
            'темно-коричневый' => ['min' => [101, 67, 33], 'max' => [139, 69, 19]],
            'фуксия' => ['min' => [255, 0, 255], 'max' => [255, 50, 255]],
            'темно-серый' => ['min' => [105, 105, 105], 'max' => [169, 169, 169]],
            'коралловый' => ['min' => [255, 127, 80], 'max' => [240, 128, 128]],
            'темно-зеленый' => ['min' => [0, 100, 0], 'max' => [0, 128, 0]],
            'коричнево-красный' => ['min' => [139, 69, 19], 'max' => [255, 0, 0]],
            'темно-бежевый' => ['min' => [205, 133, 63], 'max' => [244, 164, 96]],
            'оливковый' => ['min' => [128, 128, 0], 'max' => [85, 107, 47]],
            'шоколадный' => ['min' => [210, 105, 30], 'max' => [139, 69, 19]],
            'черно-серый' => ['min' => [105, 105, 105], 'max' => [169, 169, 169]],
            'медь' => ['min' => [184, 115, 51], 'max' => [218, 138, 103]],
            'темно-розовый' => ['min' => [153, 0, 0], 'max' => [204, 51, 51]],
            'лазурный' => ['min' => [0, 127, 255], 'max' => [0, 191, 255]],
            'кремовый' => ['min' => [255, 253, 208], 'max' => [255, 253, 208]],
            'хаки' => ['min' => [189, 183, 107], 'max' => [240, 230, 140]],
            'салатовый' => ['min' => [114, 255, 114], 'max' => [0, 100, 0]],
            'горчичный' => ['min' => [255, 219, 88], 'max' => [255, 219, 88]],
            'пурпурный' => ['min' => [128, 0, 128], 'max' => [178, 58, 238]],
            'малиновый' => ['min' => [176, 48, 96], 'max' => [199, 21, 133]],
            'светло-желтый' => ['min' => [255, 255, 224], 'max' => [255, 255, 153]],
            'темно-бордовый' => ['min' => [128, 0, 0], 'max' => [139, 0, 0]],
            'светло-синий' => ['min' => [173, 216, 230], 'max' => [135, 206, 250]],
            'перламутровый' => ['min' => [221, 160, 221], 'max' => [244, 164, 96]],
            'лиловый' => ['min' => [128, 0, 128], 'max' => [204, 204, 255]],
        ];
        $result = $this->findToDynamicParsed($image, $colorRanges);

        // Получаем размеры изображения
        $imageSize = $image->getSize();
        $imageWidth = $imageSize->getWidth();
        $imageHeight = $imageSize->getHeight();

        // Массив для хранения цветов и их количества
        $colorsCount = [];

        // Проходим по каждому пикселю изображения и подсчитываем цвета
        for ($x = 0; $x < $imageWidth; $x++) {
            for ($y = 0; $y < $imageHeight; $y++) {
                // Создаем объект PointInterface для текущей позиции
                $point = new Point($x, $y);

                // Получаем цвет пикселя
                $color = $image->getColorAt($point);

                // Получаем RGB значения цвета
                $red = $color->getValue(ColorInterface::COLOR_RED);
                $green = $color->getValue(ColorInterface::COLOR_GREEN);
                $blue = $color->getValue(ColorInterface::COLOR_BLUE);

                // Формируем строку с RGB значениями
                $colorString = "$red,$green,$blue";

                // Увеличиваем счетчик для данного цвета
                if (!isset($colorsCount[$colorString])) {
                    $colorsCount[$colorString] = 0;
                }
                $colorsCount[$colorString]++;
            }
        }

        // Сортируем массив цветов по количеству использований
        arsort($colorsCount);
    }
}
