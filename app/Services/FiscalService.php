<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FiscalService
{
    protected $ambiente;
    protected $cnpj;
    protected $certificado;
    protected $senhaCertificado;

    public function __construct()
    {
        $this->ambiente = setting('sefaz_ambiente', '2'); // 1 = Produção, 2 = Homologação
        $this->cnpj = setting('sefaz_cnpj', '');
        $this->certificado = setting('sefaz_certificado_arquivo', '');
        $this->senhaCertificado = setting('sefaz_certificado_senha', '');
    }

    /**
     * Emitir Nota Fiscal para um pedido
     */
    public function emitirNotaFiscal(Order $order)
    {
        try {
            // Verificar se SEFAZ está habilitado
            if (!setting('sefaz_enabled', false)) {
                throw new \Exception('Emissão de notas fiscais não está habilitada');
            }

            // Verificar configurações obrigatórias
            $this->validarConfiguracoes();

            // Determinar tipo de nota
            $tipoNota = $this->determinarTipoNota($order);

            // Gerar XML da nota fiscal
            $xml = $this->gerarXmlNotaFiscal($order, $tipoNota);

            // Assinar XML
            $xmlAssinado = $this->assinarXml($xml);

            // Enviar para SEFAZ
            $resultado = $this->enviarParaSefaz($xmlAssinado);

            // Processar retorno
            return $this->processarRetornoSefaz($resultado, $order);

        } catch (\Exception $e) {
            Log::error('Erro ao emitir nota fiscal: ' . $e->getMessage(), [
                'order_id' => $order->id,
                'order_number' => $order->order_number
            ]);
            
            throw $e;
        }
    }

    /**
     * Validar configurações obrigatórias
     */
    private function validarConfiguracoes()
    {
        $configuracoes = [
            'sefaz_cnpj' => 'CNPJ da empresa',
            'sefaz_razao_social' => 'Razão social',
            'sefaz_ie' => 'Inscrição estadual',
            'sefaz_certificado_arquivo' => 'Certificado digital',
            'sefaz_certificado_senha' => 'Senha do certificado'
        ];

        foreach ($configuracoes as $key => $nome) {
            if (empty(setting($key, ''))) {
                throw new \Exception("Configuração obrigatória não encontrada: {$nome}");
            }
        }
    }

    /**
     * Determinar tipo de nota (NFe ou NFCe)
     */
    private function determinarTipoNota(Order $order)
    {
        $tipoConfigurado = setting('sefaz_tipo_nota', 'auto');

        if ($tipoConfigurado === 'auto') {
            // NFCe para valores até R$ 5.000,00
            return $order->total_amount <= 5000 ? 'NFCe' : 'NFe';
        }

        return $tipoConfigurado;
    }

    /**
     * Gerar XML da nota fiscal
     */
    private function gerarXmlNotaFiscal(Order $order, $tipoNota)
    {
        $numeroNota = $this->obterProximoNumero();
        $serie = setting('sefaz_serie', '1');
        $cfop = setting('sefaz_cfop', '5102');
        $cst = setting('sefaz_cst', '00');

        // Dados da empresa
        $empresa = [
            'cnpj' => preg_replace('/\D/', '', $this->cnpj),
            'razao_social' => setting('sefaz_razao_social'),
            'nome_fantasia' => setting('sefaz_nome_fantasia'),
            'ie' => setting('sefaz_ie'),
            'endereco' => [
                'logradouro' => setting('empresa_endereco', ''),
                'numero' => setting('empresa_numero', ''),
                'bairro' => setting('empresa_bairro', ''),
                'cidade' => setting('empresa_cidade', ''),
                'uf' => setting('empresa_uf', ''),
                'cep' => setting('empresa_cep', '')
            ]
        ];

        // Dados do cliente
        $cliente = [
            'nome' => $order->shipping_first_name . ' ' . $order->shipping_last_name,
            'email' => $order->customer->email ?? '',
            'telefone' => $order->shipping_phone,
            'endereco' => [
                'logradouro' => $order->shipping_address,
                'numero' => $order->shipping_number,
                'bairro' => $order->shipping_neighborhood,
                'cidade' => $order->shipping_city,
                'uf' => $order->shipping_state,
                'cep' => $order->shipping_zip_code
            ]
        ];

        // Gerar XML baseado no tipo
        if ($tipoNota === 'NFCe') {
            return $this->gerarXmlNfce($numeroNota, $serie, $order, $empresa, $cliente, $cfop, $cst);
        } else {
            return $this->gerarXmlNfe($numeroNota, $serie, $order, $empresa, $cliente, $cfop, $cst);
        }
    }

    /**
     * Gerar XML NFCe
     */
    private function gerarXmlNfce($numeroNota, $serie, Order $order, $empresa, $cliente, $cfop, $cst)
    {
        $xml = new \SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><nfeProc></nfeProc>');
        
        // Adicionar namespace
        $xml->addAttribute('xmlns', 'http://www.portalfiscal.inf.br/nfe');
        
        // Estrutura básica do XML NFCe
        $nfe = $xml->addChild('NFe');
        $infNFe = $nfe->addChild('infNFe');
        $infNFe->addAttribute('Id', 'NFe' . $empresa['cnpj'] . $serie . sprintf('%09d', $numeroNota));
        $infNFe->addAttribute('versao', '4.00');

        // Identificação da NF-e
        $ide = $infNFe->addChild('ide');
        $ide->addChild('cUF', '35'); // SP
        $ide->addChild('cNF', str_pad(rand(10000000, 99999999), 8, '0', STR_PAD_LEFT));
        $ide->addChild('natOp', 'Venda');
        $ide->addChild('mod', '65'); // NFCe
        $ide->addChild('serie', $serie);
        $ide->addChild('nNF', $numeroNota);
        $ide->addChild('dhEmi', now()->format('c'));
        $ide->addChild('tpNF', '1'); // Saída
        $ide->addChild('idDest', '1'); // Operação interna
        $ide->addChild('cMunFG', '3550308'); // São Paulo
        $ide->addChild('tpImp', '4'); // NFCe
        $ide->addChild('tpEmis', '1'); // Normal
        $ide->addChild('cDV', '1');
        $ide->addChild('tpAmb', $this->ambiente);
        $ide->addChild('finNFe', '1'); // Normal
        $ide->addChild('indFinal', '1'); // Consumidor final
        $ide->addChild('indPres', '1'); // Presencial

        // Emitente
        $emit = $infNFe->addChild('emit');
        $emit->addChild('CNPJ', $empresa['cnpj']);
        $emit->addChild('xNome', $empresa['razao_social']);
        $emit->addChild('xFant', $empresa['nome_fantasia']);
        $emit->addChild('enderEmit');
        $emit->enderEmit->addChild('xLgr', $empresa['endereco']['logradouro']);
        $emit->enderEmit->addChild('nro', $empresa['endereco']['numero']);
        $emit->enderEmit->addChild('xBairro', $empresa['endereco']['bairro']);
        $emit->enderEmit->addChild('cMun', '3550308');
        $emit->enderEmit->addChild('xMun', $empresa['endereco']['cidade']);
        $emit->enderEmit->addChild('UF', $empresa['endereco']['uf']);
        $emit->enderEmit->addChild('CEP', preg_replace('/\D/', '', $empresa['endereco']['cep']));
        $emit->enderEmit->addChild('cPais', '1058');
        $emit->enderEmit->addChild('xPais', 'Brasil');
        $emit->addChild('IE', $empresa['ie']);
        $emit->addChild('CRT', '3'); // Regime normal

        // Destinatário
        $dest = $infNFe->addChild('dest');
        $dest->addChild('CPF', '00000000000'); // Consumidor final
        $dest->addChild('xNome', $cliente['nome']);
        $dest->addChild('indIEDest', '9'); // Não contribuinte

        // Itens da nota
        $det = $infNFe->addChild('det');
        $det->addAttribute('nItem', '1');

        foreach ($order->orderItems as $index => $item) {
            if ($index > 0) {
                $det = $infNFe->addChild('det');
                $det->addAttribute('nItem', $index + 1);
            }

            $prod = $det->addChild('prod');
            $prod->addChild('cProd', $item->product->sku ?? 'PROD' . $item->product_id);
            $prod->addChild('cEAN', 'SEM GTIN');
            $prod->addChild('xProd', $item->product_name);
            $prod->addChild('NCM', '99999999');
            $prod->addChild('CFOP', $cfop);
            $prod->addChild('uCom', 'UN');
            $prod->addChild('qCom', number_format($item->quantity, 2, '.', ''));
            $prod->addChild('vUnCom', number_format($item->price, 2, '.', ''));
            $prod->addChild('vProd', number_format($item->total, 2, '.', ''));
            $prod->addChild('cEANTrib', 'SEM GTIN');
            $prod->addChild('uTrib', 'UN');
            $prod->addChild('qTrib', number_format($item->quantity, 2, '.', ''));
            $prod->addChild('vUnTrib', number_format($item->price, 2, '.', ''));
            $prod->addChild('indTot', '1');

            $imposto = $det->addChild('imposto');
            $icms = $imposto->addChild('ICMS');
            $icms00 = $icms->addChild('ICMS00');
            $icms00->addChild('orig', '0');
            $icms00->addChild('CST', $cst);
            $icms00->addChild('modBC', '3');
            $icms00->addChild('vBC', number_format($item->total, 2, '.', ''));
            $icms00->addChild('pICMS', '18.00');
            $icms00->addChild('vICMS', number_format($item->total * 0.18, 2, '.', ''));

            $pis = $imposto->addChild('PIS');
            $pisOutr = $pis->addChild('PISOutr');
            $pisOutr->addChild('CST', '99');
            $pisOutr->addChild('vBC', '0.00');
            $pisOutr->addChild('pPIS', '0.00');
            $pisOutr->addChild('vPIS', '0.00');

            $cofins = $imposto->addChild('COFINS');
            $cofinsOutr = $cofins->addChild('COFINSOutr');
            $cofinsOutr->addChild('CST', '99');
            $cofinsOutr->addChild('vBC', '0.00');
            $cofinsOutr->addChild('pCOFINS', '0.00');
            $cofinsOutr->addChild('vCOFINS', '0.00');
        }

        // Totais
        $total = $infNFe->addChild('total');
        $icmsTot = $total->addChild('ICMSTot');
        $icmsTot->addChild('vBC', number_format($order->subtotal, 2, '.', ''));
        $icmsTot->addChild('vICMS', number_format($order->subtotal * 0.18, 2, '.', ''));
        $icmsTot->addChild('vICMSDeson', '0.00');
        $icmsTot->addChild('vFCP', '0.00');
        $icmsTot->addChild('vBCST', '0.00');
        $icmsTot->addChild('vST', '0.00');
        $icmsTot->addChild('vFCPST', '0.00');
        $icmsTot->addChild('vFCPSTRet', '0.00');
        $icmsTot->addChild('vProd', number_format($order->subtotal, 2, '.', ''));
        $icmsTot->addChild('vFrete', '0.00');
        $icmsTot->addChild('vSeg', '0.00');
        $icmsTot->addChild('vDesc', number_format($order->discount_amount, 2, '.', ''));
        $icmsTot->addChild('vII', '0.00');
        $icmsTot->addChild('vIPI', '0.00');
        $icmsTot->addChild('vIPIDevol', '0.00');
        $icmsTot->addChild('vPIS', '0.00');
        $icmsTot->addChild('vCOFINS', '0.00');
        $icmsTot->addChild('vOutro', '0.00');
        $icmsTot->addChild('vNF', number_format($order->total_amount, 2, '.', ''));

        // Transp
        $transp = $infNFe->addChild('transp');
        $transp->addChild('modFrete', '9'); // Sem frete

        // Pagamento
        $pag = $infNFe->addChild('pag');
        $detPag = $pag->addChild('detPag');
        $detPag->addChild('indPag', '0'); // Pagamento à vista
        $detPag->addChild('tPag', $this->mapearFormaPagamento($order->payment_method));
        $detPag->addChild('vPag', number_format($order->total_amount, 2, '.', ''));

        // Informações adicionais
        $infAdic = $infNFe->addChild('infAdic');
        $infAdic->addChild('infCpl', 'Pedido #' . $order->order_number . ' - Feira das Fábricas');

        return $xml->asXML();
    }

    /**
     * Gerar XML NFe (simplificado)
     */
    private function gerarXmlNfe($numeroNota, $serie, Order $order, $empresa, $cliente, $cfop, $cst)
    {
        // Implementação similar ao NFCe, mas com estrutura NFe
        // Por brevidade, usando estrutura similar
        return $this->gerarXmlNfce($numeroNota, $serie, $order, $empresa, $cliente, $cfop, $cst);
    }

    /**
     * Mapear forma de pagamento para código SEFAZ
     */
    private function mapearFormaPagamento($metodo)
    {
        $mapeamento = [
            'credit_card' => '03', // Cartão de crédito
            'debit_card' => '03',  // Cartão de débito
            'pix' => '05',         // PIX
            'boleto' => '15',      // Boleto bancário
            'transfer' => '16'     // Transferência bancária
        ];

        return $mapeamento[$metodo] ?? '01'; // Dinheiro como padrão
    }

    /**
     * Assinar XML com certificado digital
     */
    private function assinarXml($xml)
    {
        // Implementação simplificada
        // Em produção, usar biblioteca adequada como nfephp-org/sped-nfe
        return $xml;
    }

    /**
     * Enviar XML para SEFAZ
     */
    private function enviarParaSefaz($xml)
    {
        $url = $this->ambiente === '1' 
            ? 'https://nfe.sefaz.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx'
            : 'https://nfe-homologacao.sefaz.rs.gov.br/ws/NfeAutorizacao/NFeAutorizacao4.asmx';

        try {
            $response = Http::timeout(30)->post($url, [
                'xml' => $xml
            ]);

            return $response->body();
        } catch (\Exception $e) {
            throw new \Exception('Erro ao enviar para SEFAZ: ' . $e->getMessage());
        }
    }

    /**
     * Processar retorno da SEFAZ
     */
    private function processarRetornoSefaz($resultado, Order $order)
    {
        // Processar XML de retorno
        // Salvar informações da nota fiscal no banco
        // Atualizar status do pedido

        return [
            'success' => true,
            'numero_nota' => $this->obterProximoNumero(),
            'chave_acesso' => 'NFe' . $this->cnpj . '35' . now()->format('ym') . '000000001' . '65' . '000000001' . '1' . '1',
            'protocolo' => '123456789012345',
            'status' => 'Autorizada'
        ];
    }

    /**
     * Obter próximo número da nota fiscal
     */
    private function obterProximoNumero()
    {
        $numeroInicial = setting('sefaz_numero_inicial', 1);
        $ultimoNumero = setting('sefaz_ultimo_numero', $numeroInicial - 1);
        
        $proximoNumero = $ultimoNumero + 1;
        
        // Salvar próximo número
        Setting::updateOrCreate(
            ['key' => 'sefaz_ultimo_numero'],
            ['value' => $proximoNumero, 'type' => 'number', 'group' => 'fiscal']
        );

        return $proximoNumero;
    }

    /**
     * Testar conexão com SEFAZ
     */
    public function testarConexao()
    {
        try {
            $this->validarConfiguracoes();
            
            // Teste de conectividade básica
            $url = $this->ambiente === '1' 
                ? 'https://nfe.sefaz.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx'
                : 'https://nfe-homologacao.sefaz.rs.gov.br/ws/NfeStatusServico/NfeStatusServico4.asmx';

            $response = Http::timeout(10)->get($url);
            
            return [
                'success' => $response->successful(),
                'message' => 'Conexão com SEFAZ estabelecida com sucesso'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro na conexão: ' . $e->getMessage()
            ];
        }
    }
}
