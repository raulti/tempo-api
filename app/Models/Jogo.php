<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Responsável por 'Jogo'.
 */
class Jogo extends Model
{
    public $jogo;
    public $totalKills;
    public $players;
    public $kills;
    /**
     * @return mixed
     */
    public function getJogo()
    {
        return $this->jogo;
    }

    /**
     * @param mixed $jogo
     */
    public function setJogo($jogo)
    {
        $this->jogo = $jogo;
    }

    /**
     * @return mixed
     */
    public function getTotalKills()
    {
        return $this->totalKills;
    }

    /**
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }

    /**
     * @return mixed
     */
    public function getKills()
    {
        return $this->kills;
    }

    /**
     * @param mixed $totalKills
     */
    public function setTotalKills($totalKills)
    {
        $this->totalKills = $totalKills;
    }

    /**
     * @param mixed $players
     */
    public function setPlayers($players)
    {
        $this->players = $players;
    }

    /**
     * @param mixed $kills
     */
    public function setKills($kills)
    {
        $this->kills = $kills;
    }
}