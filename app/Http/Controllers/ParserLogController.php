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
     */
    public function getListaJogosDetalhes(Request $request)
    {
        return $this->parserLogBO->getListaJogosDetalhes($request->file('file0'));
    }
}
