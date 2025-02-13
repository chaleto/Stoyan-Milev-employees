<?php

namespace App\Service;

use DateMalformedStringException;
use DateTime;
use Exception;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

readonly class EmployeeService
{
    /**
     * @throws DateMalformedStringException
     * @throws Exception
     */
    public function findEmployeePairs(UploadedFile $file): array
    {
        $rows = array_map('str_getcsv', file($file->getPathname()));
        $data = $this->parseCsvRows($rows);
        $pairs = [];

        // Loop through both of array so it can make a comparison between both employee ids
        foreach ($data as $data_0) {
            foreach ($data as $data_1) {
                if ($data_0['projectId'] === $data_1['projectId'] && $data_0['employeeId'] !== $data_1['employeeId']) {
                    if ($data_0['dateFrom'] > $data_0['dateTo'] || $data_1['dateFrom'] > $data_1['dateTo']) {
                        // Ignores wrong dates
                        continue;
                    }
                    $overlap = $this->calculateOverlap($data_0, $data_1);
                    // Sort the employee IDs to ensure a consistent pair order
                    $employee_1 = max($data_0['employeeId'], $data_1['employeeId']);
                    $employee_0 = min($data_0['employeeId'], $data_1['employeeId']);

                    if ($overlap > 0) {
                        // Creating unique key to prevent duplication
                        $uniqueKey = "{$employee_0}-{$employee_1}-{$data_0['projectId']}";
                        $pairs[$uniqueKey] = [
                            'projectId' => $data_0['projectId'],
                            'employeeId_0' => $employee_0,
                            'employeeId_1' => $employee_1,
                            'overlap' => $overlap,
                        ];
                    }
                }
            }
        }

        usort($pairs, function ($a, $b) {
            if (!is_array($a) || !is_array($b)) {
                throw new Exception("Sorting error: One of the elements is not an array. a: " . var_export($a, true) . " b: " . var_export($b, true));
            }
            return $b['overlap'] <=> $a['overlap'];
        });

        return $pairs[0];
    }

    // Extract the data from csv file
    private function parseCsvRows(array $rows): array
    {
        $data = [];
        foreach ($rows as $row) {
            if (count($row) !== 4 || empty($row) || !is_numeric($row[0]) || !is_numeric($row[1])) continue;
            try {
                $dateFrom = $row[2] === null ? new DateTime('now') : $this->parseDate($row[2]);
                $dateTo = $row[3] === null ? new DateTime('now') : $this->parseDate($row[3]);

                $data[] = [
                    'employeeId' => (int)$row[0],
                    'projectId' => (int)$row[1],
                    'dateFrom' => $dateFrom,
                    'dateTo' => $dateTo,
                ];
            } catch (Exception $e) {
                continue;
            }
        }
        return $data;
    }


    /**
     * // Formatting all date variants
     * @param string $dateString
     * @return DateTime|false
     */
    private function parseDate(string $dateString): DateTime
    {
        // List of supported date formats
        $formats = [
            'd-m-Y', // dd-mm-yyyy (e.g., 01-01-2020)
            'm-d-Y', // mm-dd-yyyy (e.g., 01-01-2020)
            'Y-m-d', // yyyy-mm-dd (e.g., 2020-01-01)
            'Y/m/d', // yyyy/mm/dd (e.g., 2020/01/01)
            'm/d/Y', // mm/dd/yyyy (e.g., 01/01/2020)
            'd/m/Y', // dd/mm/yyyy (e.g., 01/01/2020)
            'Ymd',   // yyyymmdd (e.g., 20200101)
            'd-m-y', // dd-mm-yy (e.g., 01-01-20 → 2020-01-01)
            'm-d-y', // mm-dd-yy (e.g., 01-01-20 → 2020-01-01),
            'F j, Y',     // Full month name, day, year (e.g., "December 2, 2010")
            'j F Y',      // Day, full month name, year (e.g., "2 December 2010")
            'Y, F j',     // Year, full month name, day (e.g., "2010, December 2")
            'F j Y',      // Full month name, day, year (e.g., "December 2 2010")
            'j F, Y',     // Day, full month name, year (e.g., "2 December, 2010")
        ];

        // Cleaning ambiguous formats
        foreach ($formats as $format) {
            $date = DateTime::createFromFormat($format, $dateString);
            if ($date !== false) {
                if ($format === 'd-m-Y' || $format === 'd-m-y' || $format === 'm-d-y') {
                    $year = (int)$date->format('Y');
                    if ($year < 100) {
                        $date->setDate($year + 2000, $date->format('m'), $date->format('d'));
                    }
                }
                return $date;
            }
        }

        // If no format matches, throw an exception
        throw new RuntimeException("Failed to parse date: $dateString");
    }
    /**
     * @throws DateMalformedStringException
     * // Calculating days in which both employees have been working on same project
     */
    private function calculateOverlap(array $employee_0, array $employee_1): int
    {
        $start = max($employee_0['dateFrom'], $employee_1['dateFrom']);
        $end = min($employee_0['dateTo'], $employee_1['dateTo']);

        if ($start > $end) return 0;
        return $end->diff($start)->days + 1;
    }

}