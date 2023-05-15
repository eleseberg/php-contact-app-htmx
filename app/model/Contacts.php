<?php

namespace App\model;

class Contacts
{
    public string $filePath;
    public array $contactList;

    /**
     * Constructor
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $this->filePath = realpath(__DIR__ . "/../../public/data.json");
        $this->loadDb();
    }

    /**
     * Return all contact records
     *
     * @return array[]
     */
    public function all(): array
    {
        return $this->contactList;
    }

    /**
     * Search contact records for given text
     *
     * @param string $text
     * @return array
     */
    public function search(string $text): array
    {
        $result = [];
        foreach ($this->contactList as $record) {
            if (in_array($text, $record)) {
                $result[] = $record;
            }
        }
        return $result;
    }

    /**
     * Load contact records
     *
     * @return void
     * @throws \Exception
     */
    private function loadDb(): void
    {
        // Read JSON data from file
        $jsonData = file_get_contents($this->filePath);

        // Convert JSON to array
        $data = json_decode($jsonData, true);

        // Check if JSON decoding was successful
        if ($data === null) {
            throw new \Exception('Error decoding JSON data.');
        } else {
            // Use the $data array as needed
            $this->contactList = $data;
        }
    }

}