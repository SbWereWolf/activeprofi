<?php
/**
 * © SbWereWolf activeprofi
 * 2020.02.08
 */

/**
 * Copyright © 2019 Volkhin Nikolay
 * Project: activeprofi
 * DateTime: 25.06.2019 0:19
 */

namespace BusinessLogic\Generator;


use BusinessLogic\Task\Task;
use DataStorage\Basis\DataSource;
use DataStorage\Task\TaskHandler;
use Exception;

class Manager
{
    /* End Of Line symbol of file with words */
    const WORDS_EOL = "\r\n";
    /* Not A Number */
    const NAN = -1;
    private $dataSource = null;

    function __construct(DataSource $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function process(): bool
    {
        $nouns = self::getWords(CONFIGURATION_ROOT . 'noun.txt');
        $adjectives = self::getWords(CONFIGURATION_ROOT . 'adjective.txt');

        $nounsNumber = count($nouns);
        $adjectivesNumber = count($adjectives);
        $isSuccess = ($nounsNumber > 10 && $adjectivesNumber > 10);
        if ($isSuccess) {
            $nounsNumber--;
            $adjectivesNumber--;
            $handler = new TaskHandler($this->getDataSource());
            $handler->beginTransaction();
            for ($counter = 0; $counter < 1000; $counter++) {

                $titleNoun = self::getRandomInt($nounsNumber);
                $titleAdjective = self::getRandomInt($adjectivesNumber);
                $date = self::getRandomInt(1666666666);
                $authorAdjective = self::getRandomInt($adjectivesNumber);
                $statusAdjective = self::getRandomInt($adjectivesNumber);

                $isSuccess = ($titleNoun != self::NAN
                    && $titleAdjective != self::NAN
                    && $date != self::NAN
                    && $authorAdjective != self::NAN
                    && $statusAdjective != self::NAN);

                if ($isSuccess) {
                    $task = (new Task())
                        ->setTitle("$adjectives[$titleAdjective] $nouns[$titleNoun]")
                        ->setDateNumber($date)
                        ->setAuthor($adjectives[$authorAdjective])
                        ->setStatus($adjectives[$statusAdjective])
                        ->setDescription("$nouns[$titleNoun] $adjectives[$titleAdjective]"
                         . " $adjectives[$statusAdjective]");
                    $handler->registerTask($task);
                }
            }
            $handler->finishTransaction();
        }


        return true;
    }

    private static function getWords($path): array
    {
        $bulkSymbols = file_get_contents($path);

        $isSuccess = $bulkSymbols !== false;
        $rawWords = array();
        if ($isSuccess) {
            $rawWords = explode(self::WORDS_EOL, $bulkSymbols);
        }

        $pureWords = [];
        foreach ($rawWords as $word) {
            $isEmpty = empty($word);
            if (!$isEmpty) {
                $pureWords[] = $word;
            }
        }
        return $pureWords;
    }

    /**
     * @return DataSource
     */
    private function getDataSource(): DataSource
    {
        return $this->dataSource;
    }

    private static function getRandomInt($max): int
    {
        try {
            $index = random_int(0, $max);
        } catch (Exception $e) {
            $index = self::NAN;
        }
        return $index;
    }
}
