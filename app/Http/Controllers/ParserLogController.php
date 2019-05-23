<?php

namespace App\Http\Controllers;

use App\BO\ParserLogBO;
use App\Models\Jogo;
use Illuminate\Http\Request;
use Storage;

/**
 * Classe responsável por controlar as funcionalidades do parse do arquivo
 * @author raul
 */
class ParserLogController extends Controller
{
    
    protected $parserLogBO;
    
    public function __construct(ParserLogBO $parserLogBO)
    {
        $this->parserLogBO = $parserLogBO;
    }
    
    /**
     * Metodo responsável por retornar os dados do parse
     * @param Request $request
     */
    public function parse(Request $request)
    {
        //Divide jogos em Arrays
        $path = $request->file('file0')->store('upload');
        $file = explode("InitGame", Storage::get($path));
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
        //$this->parserLogBO->parse($request->file('file0'));
    }
}
