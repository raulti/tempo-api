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
            $jogo['jogo'] = 'game_' . ($key + 1);
            
            $coutTotalKills = 0;
            $nomesKilleds = [];
            foreach ($jogoData as $linhaJogo) {
                if (strpos($linhaJogo, 'killed') !== false) {
                    $coutTotalKills ++;
                    
                    //Recupera nome de jogadores Killeds
                    if (preg_match('/killed (.*?) by/', $linhaJogo, $match) == 1) {
                        $nomesKilleds[] = $match[1];
                    }
                    
                };
            }
            
             
            
            $jogo['totalKills'] = $coutTotalKills;
            $jogo['kills'] = array_count_values(array_map('strtolower', $nomesKilleds));
            $listaJogosDetalhes[] = $jogo;
        }
        
        
        return $listaJogosDetalhes;
        //$this->parserLogBO->parse($request->file('file0'));
    }
}
