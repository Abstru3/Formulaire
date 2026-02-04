<?php

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpClient\HttpClient;

class PageTest extends TestCase
{
    private const BASE_URL = 'http://localhost:8080';

    private $client;
    private $pdo;
    private array $insertedIds = [];

    protected function setUp(): void
    {
        $this->client = HttpClient::create([
            'headers' => [
                'Accept' => 'text/html',
            ],
        ]);

        $host = '127.0.0.1';
        $dbname = 'formulaire';
        $user = 'root';
        $password = 'root';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
        } catch (Exception $e) {
            $this->fail("Erreur connexion BDD: " . $e->getMessage());
        }
    }

    protected function tearDown(): void
    {
        if (!empty($this->insertedIds)) {
            $stmt = $this->pdo->prepare("DELETE FROM tirages WHERE id IN (" . implode(',', $this->insertedIds) . ")");
            $stmt->execute();
        }

        $this->pdo = null;
    }

    public function testPageLoads(): void
    {
        $response = $this->client->request('GET', self::BASE_URL);
        $statusCode = $response->getStatusCode();

        $this->assertEquals(200, $statusCode);
    }

    public function testFormSubmit(): void
    {
        $stmt = $this->pdo->query("SELECT COUNT(*) AS count FROM tirages");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = (int)$result['count'];

        $stmt = $this->pdo->query("SELECT MAX(id) AS max_id FROM tirages");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $maxIdBefore = (int)$result['max_id'];

        $participants = [
            'c1' => '1',
            'c2' => '2',
            'c3' => '3',
            'c4' => '4',
            'c5' => '5',
            'c6' => '6',
            'c7' => '7',
            'c8' => '8',
            'c9' => '9',
            'c10' => '10',
        ];

        $response = $this->client->request('POST', self::BASE_URL . '/tirage.php', [
            'body' => $participants,
        ]);

        $statusCode = $response->getStatusCode();
        $content = $response->getContent();

        $this->assertEquals(200, $statusCode);
        $this->assertStringContainsString('Le gagnant est :', $content);
        $this->assertStringContainsString('Champ tiré :', $content);

        $isValid = false;
        foreach ($participants as $key => $value) {
            if (str_contains($content, "Le gagnant est : $value")) {
                $isValid = true;
                break;
            }
        }

        $this->assertTrue($isValid, 'winner is invalid');

        $stmt = $this->pdo->query("SELECT COUNT(*) AS count FROM tirages");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $updatedCount = (int)$result['count'];

        $this->assertEquals($count + 1, $updatedCount, 'winner entry not found');

        $stmt = $this->pdo->query("SELECT id FROM tirages WHERE id > $maxIdBefore");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $this->insertedIds[] = $row['id'];
        }
    }
}
