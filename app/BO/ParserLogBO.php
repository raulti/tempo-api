<?php

namespace App\BO;

use File;
use Storage;

/**
 * Resonsável por implementar as regras de negócio de 'Parse do Log'
 */
class ParserLogBO
{
    /**
     * Retorna os detalhes dos jogos
     */
    public function getListaJogosDetalhes($file)
    {
        $log = $this->saveAndGetLocalFile($file);
        $arrayJogos = $this->getArrayJogos($log);
        return $this->getDetalhesJogos($arrayJogos);
    }
    
    /**
     * Salva arquivo de logo em uma pasta e retorna o mesmo
     */
    public function saveAndGetLocalFile($file){
         $path = $file->store('upload');
         return Storage::get($path);
    }
    
    /**
     * Separa os jogos.
     */
    public function getArrayJogos($log) {
        $jogosExplode = explode("InitGame", $log);       
        $arrayJogos = [];
        foreach ( $jogosExplode as $jogoExplode ) {
            $arrayJogos[] = array_filter(array_map("trim", explode("\n", $jogoExplode)));
        }
        
        return $arrayJogos;
    }
    
    /**
     * Retorna os dados dos jogos
     */
    public function getDetalhesJogos($arrayJogos) {
   
        $listaJogosDetalhes = [];
        foreach ($arrayJogos as $key => $jogoData){
            
            $jogo = [];
            $jogo['jogo'] = 'game_' . ($key);
            
            $coutTotalKills = 0;
            $nomesKills = [];
            $arrayNomesKilledsWorld = [];
            $arrayNomesPlayers = [];
            
            //Recupera quantidades de kills
            foreach ($jogoData as $linhaJogo) {
                if (strpos($linhaJogo, 'killed') !== false) {
                    $coutTotalKills ++;
                    
                    //Recupera nome de jogadores Killeds
                    preg_match('/: (.*?) killed/', $linhaJogo, $match);
                    $arrayNameKiller = explode(": ", $match[1]);
                    $nameKiller = $arrayNameKiller[1];
                    
                    //Valida se o Killer é <world>
                    if($nameKiller != "<world>"){
                        $nomesKills[] = $nameKiller;
                    } else{
                        preg_match('/killed (.*?) by/', $linhaJogo, $match);
                        $arrayNomesKilledsWorld[] = $match[1];
                    }
                }
                
                $arrayNomesPlayers[] = $this->getPlayerNome($linhaJogo);
            }
            
            //Monta estrutura do detalhe do jogo
            $jogo['total_kills'] = $coutTotalKills;
            $jogo['kills'] = $this->removeKillWolrd($nomesKills, $arrayNomesKilledsWorld);
            $jogo['players'] = array_unique($arrayNomesPlayers);
            $listaJogosDetalhes[] = $jogo;
        }
        return $listaJogosDetalhes;
    }
    
    /**
     * Lista nomes de Players do jogo
     */
    public function getPlayerNome($linhaJogo) {
        if (strpos($linhaJogo, 'ClientUserinfoChanged') !== false) {
            $arrayLinhaJogo = explode("\\", $linhaJogo);
            return $arrayLinhaJogo[1];
        }
    }
    
    /**
     * Remove 1 Kill do player que foi morto por um <world>
     * Caso o jogador tenha matado outro jogado o código subtrai do array de Kills já existente
     * Caso o jogado durante a partida não tenha matado nenhum outro player, ele é inserido no array de Kills com contagem negativa
     */
    public function removeKillWolrd($nomesKills, $arrayNomesKilledsWorld) {
        
        //Agrupa ocorrencias de jogadores
        $uniqNomes = array_count_values(array_map('strtolower', $arrayNomesKilledsWorld));
        $nomesKills = array_count_values(array_map('strtolower', $nomesKills));
        
        foreach ($uniqNomes as $key => $uniqNome){
            
            if(array_key_exists($key, $nomesKills)){
                $nomesKills[$key] = $nomesKills[$key] - $uniqNomes[$key];
            } else {
                $nomesKills[$key] = 0 - $uniqNomes[$key];
            }
        }
        
        return $nomesKills;
    }
}
