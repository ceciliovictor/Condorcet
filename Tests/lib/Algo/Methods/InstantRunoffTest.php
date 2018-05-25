<?php
declare(strict_types=1);
namespace Condorcet;


use PHPUnit\Framework\TestCase;


class InstantRunoffTest extends TestCase
{
    /**
     * @var election
     */
    private $election;

    public function setUp()
    {
        $this->election = new Election;
    }

    public function testResult_1 ()
    {
        # From https://fr.wikipedia.org/wiki/Vote_alternatif

        $this->election->addCandidate('A');
        $this->election->addCandidate('B');
        $this->election->addCandidate('C');
        $this->election->addCandidate('D');

        $this->election->parseVotes('
            A>B>C>D * 42
            B>C>D>A * 26
            C>D>B>A * 15
            D>C>B>A * 17
        ');

        self::assertEquals( [
                1 => 'D',
                2 => 'A',
                3 => 'B',
                4 => 'C' ],
            $this->election->getResult('InstantRunoff')->getResultAsArray(true)
        );

        self::assertSame(
            [
                1 =>
                    [
                        "A" => 42,
                        "B" => 26,
                        "C" => 15,
                        "D" => 17
                    ],
                2 =>
                    [
                        "A" => 42,
                        "B" => 26,
                        "D" => 32
                    ],
                3 =>
                    [
                        "A" => 42,
                        "D" => 58
                    ]
            ],
            $this->election->getResult('InstantRunoff')->getStats()
        );

    }

    public function testResult_2 ()
    {
        # From https://en.wikipedia.org/wiki/Instant-runoff_voting#Examples

        $this->election->addCandidate('bob');
        $this->election->addCandidate('sue');
        $this->election->addCandidate('bill');

        $this->election->parseVotes('
            bob > bill > sue
            sue > bob > bill
            bill > sue > bob
            bob > bill > sue
            sue > bob > bill
        ');

        self::assertEquals( [
            1 => 'sue',
            2 => 'bob',
            3 => 'bill' ],
            $this->election->getResult('InstantRunoff')->getResultAsArray(true)
        );

    }

    public function testResult_3 ()
    {
        $this->election->addCandidate('bob');
        $this->election->addCandidate('sue');
        $this->election->addCandidate('bill');

        $this->election->parseVotes('
            bob > bill > sue
            sue > bob > bill
            bill > sue > bob
            bob > bill > sue
            sue > bob > bill
            bill > bob > sue
        ');

        self::assertEquals( [
            1 => 'bob',
            2 => 'bill',
            3 => 'sue' ],
            $this->election->getResult('InstantRunoff')->getResultAsArray(true)
        );

    }

    public function testResult_4 ()
    {
        $this->election->addCandidate('A');
        $this->election->addCandidate('B');
        $this->election->addCandidate('C');

        $this->election->parseVotes('
            A=B=C
        ');

        self::assertEquals( [
            1 => ['A','B','C'] ],
            $this->election->getResult('InstantRunoff')->getResultAsArray(true)
        );

    }

}