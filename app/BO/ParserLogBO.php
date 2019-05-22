<?php

namespace App\BO;

use App\Repositories\PessoaRepository;

/**
 * Resonsável por implementar as regras de negócio de 'Pessoa'
 */
class ParserLogBO
{
    protected $pessoaRepository;

    public function __construct(PessoaRepository $pessoaRepository)
    {
        $this->pessoaRepository = $pessoaRepository;
    }

    public function salvar($pessoa)
    {
       return $this->pessoaRepository->salvar($pessoa);
    }

    public function getPessoaPorId($id)
    {
        return $this->pessoaRepository->getPessoaPorId($id);
    }

    public function excluir($id)
    {
        return $this->pessoaRepository->excluir($id);
    }
    
    public function list() {
        return $this->pessoaRepository->getPessoasAtivas();
    }
}
