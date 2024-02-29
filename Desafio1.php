<?php 

try{
	$db = Db::Read()->clear()
		->select([
			'a.dscapresentacao',
			'a.dthr_apresentacao',
		    'a.dsclocal',
		    'a.capacidade'
			])
		->from('si_apresentacao a')
		->fetchall();
	
	foreach($db as &$banco){
		$banco['pagantes'] = Db::Read()->clear()->selectCount()
										  ->from('pdv_produto_mov_item')
										  ->whereAND(['idapresentacao' => $banco['idapresentacao'], 'valor' => new we_Where_NotEqual(0)])
										  ->fetchColumn();
		$banco['cortesias'] = Db::Read()->clear()->selectCount()
										  ->from('pdv_produto_mov_item')
										  ->whereAND(['idapresentacao' => $banco['idapresentacao'], 'valor' => 0])
										  ->fetchColumn();
		$banco['publico'] = $banco['pagantes'] + $banco['cortesias'];
		
		if($banco['capacidade'] >= 0){
		    $banco['ocupacao'] = round(($banco['capacidade'] > 0 ? ($banco['pagantes'] + $banco['cortesias']) / $banco['capacidade']*100 : 0), 2) . "%";
		    $banco['disponiveis'] = $banco['capacidade'] - $banco['publico'];
		}
	}
	
    return [
        "rows" => $db
	];
	
}catch(Exception $e){
    throw new Exception('Erro ao encontrar dados');
}