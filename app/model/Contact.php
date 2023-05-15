<?php

namespace App\model;

class Contact
{
    public string $filePath;

    /**
     * mock contacts database
     *
     * @var array
     */
    public array $db;

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
        return $this->db;
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
        foreach ($this->db as $record) {
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
            throw new \Exception('Error decoding JSON data from ' . $this->filePath);
        } else {
            // Use the $data array as needed
            $this->db = $data;
        }
    }

}