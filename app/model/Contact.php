<?php

namespace App\model;

class Contact
{
    /**
     * Mock contacts database
     *
     * @var array
     */
    private static array $db = [];
    private array $errors;

    /**
     * Constructor
     *
     * @param int|null $id
     * @param string|null $first
     * @param string|null $last
     * @param string|null $phone
     * @param string|null $email
     */
    public function __construct(
        public ?int $id,
        public ?string $first,
        public ?string $last,
        public ?string $phone,
        public ?string $email
    ) {
        $this->errors = [];
    }

    public function __toString()
    {
        return json_encode($this, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @return int|null
     */
    public function id(): ?int
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function first(): ?string
    {
        return $this->first;
    }

    /**
     * @return string|null
     */
    public function last(): ?string
    {
        return $this->last;
    }

    /**
     * @return string|null
     */
    public function phone(): ?string
    {
        return $this->phone;
    }

    /**
     * @return string|null
     */
    public function email(): ?string
    {
        return $this->email;
    }

    /**
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * @param string $first
     * @param string $last
     * @param string $phone
     * @param string $email
     * @return void
     */
    public function update(string $first, string $last, string $phone, string $email): void
    {
        $this->first = $first;
        $this->last = $last;
        $this->phone = $phone;
        $this->email = $email;
    }

    /**
     * @return bool
     */
    public function validate(): bool
    {
        if (!$this->email) {
            $this->errors['email'] = "Email Required";
        }
        $existingContact = null;
        foreach (self::$db as $contact) {
            if ($contact->id !== $this->id && $contact->email === $this->email) {
                $existingContact = $contact;
                break;
            }
        }
        if ($existingContact) {
            $this->errors['email'] = "Email Must Be Unique";
        }
        return count($this->errors) === 0;
    }

    /**
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()) {
            return false;
        }
        if ($this->id === null) {
            if (empty(self::$db)) {
                $maxId = 1;
            } else {
                $maxId = max(array_column(self::$db, 'id')) + 1;
            }
            $this->id = $maxId;
            self::$db[$this->id] = $this;
        }
        self::saveDB();
        return true;
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        unset(self::$db[$this->id]);
        self::saveDB();
    }

    /**
     * @return int
     */
    public static function count(): int
    {
        sleep(2);
        return count(self::$db);
    }

    /**
     * Return all contact records for a page
     *
     * @param int $page
     * @return array
     */
    public static function all(int $page = 1): array
    {
        $start = ($page - 1) * PAGE_SIZE;
        $end = $start + PAGE_SIZE;
        return array_slice(array_values(self::$db), $start, $end - $start);
    }

    /**
     * Search contact records for given text
     *
     * @param $text
     * @return array
     */
    public static function search($text): array
    {
        $result = [];
        foreach (self::$db as $contact) {
            if (
                str_contains($contact->first, $text) ||
                str_contains($contact->last, $text) ||
                str_contains($contact->email, $text) ||
                str_contains($contact->phone, $text)
            ) {
                $result[] = $contact;
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
    public static function loadDB(): void
    {
        $filePath = realpath(__DIR__ . "/../data/data.json");

        try {
            $contactsFile = file_get_contents($filePath);
        } catch (\Exception $e) {
            throw $e;
            //throw new \Exception('Error accessing JSON data at ' . $filePath);
        }

        $contacts = json_decode($contactsFile, true);
        self::$db = [];
        foreach ($contacts as $c) {
            self::$db[$c['id']] = new Contact($c['id'], $c['first'], $c['last'], $c['phone'], $c['email']);
        }
    }

    /**
     * @return void
     */
    public static function saveDB(): void
    {
        $filePath = realpath(__DIR__ . "/../data/data.json");

        $outArr = [];
        foreach (self::$db as $contact) {
            $outArr[] = $contact;
        }

        file_put_contents($filePath, json_encode($outArr, JSON_PRETTY_PRINT));
    }

    /**
     * @param $id
     * @return Contact
     */
    public static function find($id): Contact
    {
        $id = intval($id);
        $contact = self::$db[$id] ?? null;
        if ($contact !== null) {
            $contact->errors = [];
        }
        return $contact;
    }
}