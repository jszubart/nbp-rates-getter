<?php

namespace App\Command;

use App\Entity\Currency;
use App\Manager\CurrencyManager;
use App\Service\CurrencyService;
use App\Service\Api\NbpApiService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use UnexpectedValueException;

#[AsCommand(
    name: 'app:nbp:rates',
    description: 'This command gets current exchange rates from https://api.nbp.pl/ and store them in local database',
)]
class NbpRatesCommand extends Command
{
    public function __construct(private readonly NbpApiService $nbpApiService,
                                private readonly CurrencyService $currencyService,
                                private readonly CurrencyManager $currencyManager)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        try {
            $this->processNbpRates();
            $io->success('Exchange rates are successfully updated');
            return Command::SUCCESS;
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
            return Command::FAILURE;
        }
    }

    private function processNbpRates(): void
    {
        $exchangeRatesTableData = $this->nbpApiService->getTableExchangeRates('A');

        if (!isset($exchangeRatesTableData[0]['rates'])) {
            throw new UnexpectedValueException('No exchange rates provided');
        }

        $currenciesExchangeRates = $exchangeRatesTableData[0]['rates'];

        foreach ($currenciesExchangeRates as $currencyRate) {
            if (!isset($currencyRate['currency']) || !isset($currencyRate['code']) || !isset($currencyRate['mid'])) {
                throw new UnexpectedValueException('Wrong Data provided');
            }

            $currency = $this->currencyService->getCurrencyByCode($currencyRate['code']);

            if (!($currency instanceof Currency)) {
                $this->currencyManager->createCurrency($currencyRate['currency'], $currencyRate['code'], (string)$currencyRate['mid']);
            } else {
                $this->currencyManager->updateCurrencyExchangeRate($currency, (string)$currencyRate['mid']);
            }
        }
    }
}
