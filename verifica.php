<?php
class Correio {

	public $status;
	public $hash;
	public $erro = false;
	public $track;

	public function __construct($id=false){
		if ($id){
			if (strlen($id) == 13) $this->track ($id);
			else {
				$this->erro = true;
				$this->erro_msg = '<p id="erro">Desculpe, mas o código de encomenda &eacute; inv&aacute;lido.</p>';
			}
		}
	}

	private function track($id){
		$html = utf8_encode(file_get_contents('http://websro.correios.com.br/sro_bin/txect01$.QueryList?P_LINGUA=001&P_TIPO=001&P_COD_UNI=' . $id));
		
		if (strstr($html, '<table') === false){
			$this->erro = true;
			$this->erro_msg = '<p id="erro">Objeto ainda n&atilde;o foi adicionado no sistema.</p>';
			return;
		}

		$this->hash = md5($html);

		$html = preg_replace("@\r|\t|\n| +@", ' ', $html);
		$html = str_replace('</tr>', "</tr>\n", $html);

		if (preg_match_all('@<tr>(.*)</tr>@', $html, $mat,PREG_SET_ORDER)){
			$track = array();
			$mat = array_reverse($mat);
			$temp = null;
			foreach($mat as $item){
				if (preg_match("@<td rowspan=[12]>(.*)</td><td>(.*)</td><td><FONT COLOR=\"[0-9A-F]{6}\">(.*)</font></td>@", $item[0], $d)){
					$tmp = array(
						'data' => $d[1],
						'data_sql' => preg_replace('@([0-9]{2})/([0-9]{2})/([0-9]{4}) ([0-9]{2}):([0-9]{2})@', '$3-$2-$1 $4:$5:00',$d[1] ),
						'local' => $d[2],
						'acao' => ucfirst(strtolower($d[3])),
						'detalhes' => ''
					);

					if ($temp){
						$tmp['detalhes'] = $temp;
						$temp = null;
					}

					$track[] = (object)$tmp;
				}else if (preg_match("@<td colspan=2>(.*)</td>@", $item[0], $d)){
					$temp = $d[1];
				}
				$this->status = $tmp['acao'];
			}
			$this->track = $track;
			return;
		}

		$this->erro = true;
		$this->erro_msg = '<p id="erro">Falha de comunica&ccedil;&otilde;o com os Correios. Por favor, tente novamente mais tarde.</p>';
	}
}
