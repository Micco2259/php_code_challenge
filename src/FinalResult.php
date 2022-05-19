<?php

class FinalResult
{
    public function results($filePath)
    {
        try {
            $file = fopen($filePath, "r");
            $header = fgetcsv($file);
            $records = [];
    
            while (!feof($file)) {
                $data = fgetcsv($file);
                
                if (count($data) == 16) {
                    $amount = 0;
                    $bankAccountNumber = "Bank account number missing";
                    $bankBranchCode = "Bank branch code missing";
                    $endToEndId = "End to end id missing";
    
                    if (!empty($data[8])) {
                        $amount = (float) $data[8];
                    }
    
                    if (!empty($data[6])) {
                        $bankAccountNumber = (int) $data[6];
                    }
    
                    if (!empty($data[2])) {
                        $bankBranchCode = $data[2];
                    }
    
                    if (!empty($data[10]) && !empty($data[11])) {
                        $endToEndId = "{$data[10]}{$data[11]}";
                    }
    
                    $record = [
                        "amount" => [
                            "currency" => $header[0],
                            "subunits" => (int) ($amount * 100)
                        ],
                        "bank_account_name" => str_replace(" ", "_", strtolower($data[7])),
                        "bank_account_number" => $bankAccountNumber,
                        "bank_branch_code" => $bankBranchCode,
                        "bank_code" => $data[0],
                        "end_to_end_id" => $endToEndId,
                    ];

                    $records[] = $record;
                }
            }

            return [
                "filename" => basename($filePath),
                "document" => $file,
                "failure_code" => $header[1],
                "failure_message" => $header[2],
                "records" => $records
            ];
        } catch (\Exception $e) {
            return [
                "filename" => null,
                "document" => null,
                "failure_code" => 111,
                "failure_message" => $e->getMessage(),
                "records" => null
            ];
        }
    }
}
