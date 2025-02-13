<?php

namespace App\Command;

use App\Service\EmployeeService;
use DateMalformedStringException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsCommand(
    name: 'find-employee-pair',
    description: 'Find employee pair by name',
)]
class AppFindEmployeePairCommand extends Command
{
    public function __construct(private readonly EmployeeService $employeeService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('filePath', InputArgument::REQUIRED, 'Path to a csv file');
    }

    /**
     * @throws DateMalformedStringException
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filePath = $input->getArgument('filePath');
        $fileName = substr($filePath, strrpos($filePath, '/') + 1);

        $file = new UploadedFile($filePath, $fileName, null, null, true);
        $pairs = $this->employeeService->findEmployeePairs($file);
        if (empty($pairs)) {
            $output->writeln('No employees found.');
            return Command::SUCCESS;
        }

        $output->writeln('Employee ID #1 | Employee ID #2 | Project ID | Days Worked');

        $employeeIdWidth = 15;
        $projectIdWidth = 11;
        $overlapWidth = 12;

        $employeeId_0 = str_pad($pairs['employeeId_0'], $employeeIdWidth);
        $employeeId_1 = str_pad($pairs["employeeId_1"], $employeeIdWidth);
        $projectId = str_pad($pairs["projectId"], $projectIdWidth);
        $overlap = str_pad($pairs["overlap"], $overlapWidth);

        $output->writeln($employeeId_0 . '| ' . $employeeId_1 . '| ' . $projectId . '| ' . $overlap);
        return Command::SUCCESS;
    }
}
