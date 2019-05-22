<?php

namespace App\Http\Controllers;

use App\BO\PessoaBO;
use Illuminate\Http\Request;

class ParserLogController extends Controller
{

    protected $pessoaBO;

    public function __construct(PessoaBO $pessoaBO)
    {
        $this->pessoaBO = $pessoaBO;
    }

    public function salvar(Request $request)
    {
        $pessoa = (object)$request->all();
        
        $pessoa->st_ativo = true;
        $this->pessoaBO->salvar($pessoa);
    }
    
    public function getPessoaPorId($id)
    {
        $pessoa = $this->pessoaBO->getPessoaPorId($id);
        return view('pessoa', compact('pessoa'));
    }

    public function excluir(Request $request, $id)
    {
        $this->pessoaBO->excluir($id);
    }
    
    public function list() {
        return $this->pessoaBO->list();
    }
}
