function WebServiceExec($params, $data){
	/*
	$parametros = [
		"cpf" => "04369093082",
		"dt_niver" => "2006-03-01",
		"dt_inicial" => "2024-01-01",
		"dt_final" => "2024-03-01"
	];
	*/
	try{
		$socio = Db::Read()->clear()
    	    ->select([
				'b.cpf',
				'c.dt_inicial',
				'c.dt_final',
				'b.email',
    	        'b.celular',
				'b.nome',
				'b.dt_nascimento',
				'p.dscplano',
				'c.duracao_plano',
				'c.num_contrato',
				'a.dscestado_ativacao'
     	       ])
     	   	->from('gs_contrato c')
			->join('base_pessoa b', 'c.idpessoa = b.idpessoa')
			->join('gs_plano p', 'c.idplano = p.idplano')
			->join('gs_estado_ativacao a', 'c.idestado_ativacao = a.idestado_ativacao')
			->whereAND([
				'b.cpf' => '04369093082'
			])
			->limitDB(1)
			->fetchAll();
		
		foreach($socio as &$socio_celular) {
            $socio_celular['celular'] = explode('+55', '', $socio_celular['celular']); 
        }
		
		foreach($socio as &$socio_nome){
			$socio_nome = preg_replace(array("/(á|à|ã|â|ä)/","/(Á|À|Ã|Â|Ä)/","/(é|è|ê|ë)/","/(É|È|Ê|Ë)/","/(í|ì|î|ï)/","/(Í|Ì|Î|Ï)/","/(ó|ò|õ|ô|ö)/","/(Ó|Ò|Õ|Ô|Ö)/","/(ú|ù|û|ü)/","/(Ú|Ù|Û|Ü)/","/(ñ)/","/(Ñ)/"),explode(" ","a A e E i I o O u U n N"),$socio_nome);
			$socio_nome['nome'] = strtoupper($socio_nome['nome']);
		}
		
		foreach($socio as &$socio_idade){
			if(!empty($socio_idade['dt_nascimento'])){
            	$data_atual = new DateTime();
            	$data_nascimento = DateTime::createFromFormat('Y-m-d', $socio_idade['dt_nascimento']);
            	$idade = $data_atual->diff($data_nascimento)->y;
				$socio_idade['idade'] = $idade;
        	}else{
            	$socio_idade['idade'] = null;
        	}
		}
		
		foreach($socio as &$socio_voto){
			if($idade>=18 && new DateTime($socio_voto['dt_inicial'])<=new DateTime('2024-01-01') && new DateTime($socio_voto['dt_final'])>=new DateTime('2024-03-01')
            	&& (($socio_voto['dscestado_ativacao']=="Sócio")or
					($socio_voto['dscestado_ativacao']=="Ativo")or
					($socio_voto['dscestado_ativacao']=="Ativo-não-renovado")or
					($socio_voto['dscestado_ativacao']=="Upgrade")or
					($socio_voto['dscestado_ativacao']=="Renovado"))
        	){
            	$socio_voto['pode_votar'] = "Sim";
        	}else{
            	$socio_voto['pode_votar'] = "Não";
        	}
		}
		
	}catch(Exception $e){
		throw new Exception('Erro ao encontrar dados');
	}
	
	return var_dump($socio);
}