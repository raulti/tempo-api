<?php

namespace App\BO;

use File;
use Storage;

/**
 * Resonsável por implementar as regras de negócio de 'Parse do Log'
 */
class ParserLogBO
{
   
    public function parse($file)
    {
        $file = $this->saveAndGetLocalFile($file);
        $content = explode("InitGame", $file);
        //Divide jogos em Arrays
        $jogos = [];
        foreach ( $file as $content ) {
            $jogos[] = array_filter(array_map("trim", explode("\n", $content)));
        }
        
        //Identifica total de Kills em um jogo
        $listaJogosDetalhes = [];
        foreach ($jogos as $key => $jogoData){
            
            $jogo = [];
            $jogo['jogo'] = 'game_' . ($key);
            
            $coutTotalKills = 0;
            $nomesKills = [];
            $arrayNomesKilledsWorld = [];
            $arrayNomesPlayers = [];
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
                
                //Lista nomes de Players do jogo
                if (strpos($linhaJogo, 'ClientUserinfoChanged') !== false) {
                    $arrayLinhaJogo = explode("\\", $linhaJogo);
                    $arrayNomesPlayers[] = $arrayLinhaJogo[1];
                }
            }
            
            
            
            $nomesKills = array_count_values(array_map('strtolower', $nomesKills));
            $uniqArrayNomesPlayers = array_unique($arrayNomesPlayers);
            
            //Remove 1 Kill do player que foi morto por um <world>
            $UniqArrayNomesKilledsWorld = array_count_values(array_map('strtolower', $arrayNomesKilledsWorld));
            foreach ($UniqArrayNomesKilledsWorld as $key => $nomeKilledsWorld){
                
                if(array_key_exists($key, $nomesKills)){
                    $nomesKills[$key] = $nomesKills[$key] - $UniqArrayNomesKilledsWorld[$key];
                } else {
                    $nomesKills[$key] = 0 - $UniqArrayNomesKilledsWorld[$key];
                }
            }
            
            $jogo['total_kills'] = $coutTotalKills;
            $jogo['kills'] = $nomesKills;
            $jogo['players'] = $uniqArrayNomesPlayers;
            $listaJogosDetalhes[] = $jogo;
        }
       
        return $listaJogosDetalhes;
    }
    
    public function saveAndGetLocalFile($file){
         $path = $file->store('upload');
         return Storage::get($path);
    }
}
