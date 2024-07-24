<?php

namespace Modules\Primavera\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Modules\Clients\Entities\Clients;
use Modules\Primavera\Entities\AddressPrimavera;
use Modules\Primavera\Entities\PrimaveraClients;
use Modules\Primavera\Entities\PrimaveraClientsContacts;
use mysql_xdevapi\Exception;
use Throwable;

class PrimaveraClientsService
{

    private $auth;
    private $primaveraAuth;
    private $primaveraClient;

    public function __construct()
    {
        $this->primaveraAuth = new PrimaveraAuthService();
    }

    public function getAllClients()
    {
        $customers = $this->primaveraAuth->requestPrimaveraApi('GET', '/WebApi/ApiExtended/LstClientes');

        return $customers;
    }

    /**
     * It gets all the clients from the Primavera API, and then it updates the database with the new
     * information
     */
    public function updateOrCreateClients($command)
    {
        $customers = $this->getAllClients();

        $uniqueCustomers = [];
        foreach ($customers as $customer) {
            if (!isset($uniqueCustomers[$customer->Cliente])) {
                $uniqueCustomers[$customer->Cliente] = $customer;
            }
        }
        $allCustomers = $customers;
        $customers = array_values($uniqueCustomers);

        foreach ($customers as $customer) {
            if ($customer->Pais == 'PT') { //To facilitate and simplify the program, only sanitize Portuguese numbers
                $phones = $this->sanitizePhone($customer);
            }

            try {
                PrimaveraClients::updateOrCreate(
                    ['primavera_id' => $customer->Cliente],
                    [
                        'name' => $customer->Nome ?? "",
                        'address' => $customer->Fac_Mor ?? "",
                        'postal_code' => $customer->Fac_Cp ?? "",
                        'postal_code_address' => $customer->Fac_Cploc ?? "",
                        'country' => $customer->Pais ?? "",
                        'tax_number' => $customer->NumContrib ?? "",
                        'phone_1' => $phones["phone_1"] ?? "",
                        'phone_2' => $phones["phone_2"] ?? "",
                        'phone_3' => $phones["phone_3"] ?? "",
                        'phone_4' => $customer->Fac_Fax ?? "",
                        'phone_5' => $customer->Fac_Tel ?? "",
                        'payment_method' => $customer->ModoPag ?? "",
                        'payment_condition' => $customer->CondPag ?? "",
                        'email' => $customer->EnderecoWeb ?? "",
                        'total_debt' => $customer->TotalDeb ?? "",
                        'age_debt' => $customer->IdadeSaldoCob >= 0
                            ? $customer->IdadeSaldoCob : 0,
                        'status' => $customer->Situacao ?? "",
                        'rec_mode' => $customer->ModoRec ?? "",
                        'fiscal_name' => $customer->NomeFiscal ?? "",
                        'notes' => $customer->Notas ?? "",
                        'zone' => $customer->Zona ?? "",
                        'zone_description' => $customer->ZonaDescricao ?? "",
                        'discount_1' => $customer->DescontoCli ?? 0,
                        'discount_2' => $customer->DescontoCli2 ?? 0,
                        'discount_3' => $customer->DescontoCli3 ?? 0,
                        'cliente_anulado' => $customer->ClienteAnulado,
                        'tipo_preco' => $customer->TipoPrec,
                    ]
                );

            } catch (Throwable $th) {
                $command->error('Error to save Client ' . $customer->Cliente);
            }
        }

        foreach ($allCustomers as $customer) {
            try {
                if (!empty($customer->MoradasAlternativas)) {
                    foreach ($customer->MoradasAlternativas as $morada) {
                        $phoneNumber = $morada->Telefone;
                        $sanitizedPhoneNumber = $this->sanitizeAddressPhoneNumber($phoneNumber);

                        AddressPrimavera::updateOrCreate(
                            [
                                'primavera_id' => $customer->Cliente,
                                'alternativeAddress' => $morada->MoradaAlternativa
                            ],
                            [
                                'address' => $morada->Morada,
                                'iecCode' => $morada->CodigoIEC,
                                'iecExemptionCode' => $morada->CodigoIsencaoIEC,
                                'localCode' => $morada->CodigoLocal,
                                'postalCode' => $morada->Cp,
                                'postalCodeLocation' => $morada->CpLocalidade,
                                'district' => $morada->Distrito,
                                'eGAR_APACode' => $morada->eGAR_CodigoAPA,
                                'eGAR_IsExempt' => $morada->eGAR_Isenta,
                                'eGAR_PGLNumber' => $morada->eGAR_NumPGL,
                                'eGAR_ProducerType' => $morada->eGAR_TipoProdutor,
                                'fax' => $morada->Fax,
                                'iecExempt' => $morada->IsentoIEC,
                                'location' => $morada->Localidade,
                                'address2' => $morada->Morada2,
                                'alternativeAddress' => $morada->MoradaAlternativa,
                                'shippingAddress' => $morada->MoradaFacturacao,
                                'billingAddress' => $morada->MoradaPorDefeito,
                                'country' => $morada->Pais,
                                'phone' => $sanitizedPhoneNumber[0],
                                'phone_2' => $sanitizedPhoneNumber[1] ?? null,
                                'senderType' => $morada->TipoRemetente,
                                'lastUpdateVersion' => $morada->VersaoUltAct,
                                'is_contact' => false,
                            ]
                        );
                    }
                }

                if (!empty($customer->Contactos)) {
                    foreach ($customer->Contactos as $contacto) {
                        AddressPrimavera::updateOrCreate( //Populate an cliente address with a contact from Primavera CRM client contacts 
                            [
                                'primavera_id' => $customer->Cliente,
                                'address' => $contacto->PrimeiroNome . ' ' . $contacto->NomesIntermedios . ' ' . $contacto->UltimoNome,
                            ],
                            [
                                'address' => $contacto->PrimeiroNome . ' ' . $contacto->NomesIntermedios . ' ' . $contacto->UltimoNome, //CRM contact usualy has the address as the names. Ex: Amaral e Filhos, Loja - 05 - COVADA
                                'postalCode' => $contacto->CodPostal,
                                'postalCodeLocation' => $contacto->CodPostalLocal,
                                'fax' => $contacto->Fax,
                                'location' => $contacto->Localidade,
                                'address2' => $contacto->Morada2,
                                'alternativeAddress' => $contacto->MoradaResid,
                                'country' => $contacto->Pais,
                                'phone' => $contacto->TelefoneContacto,
                                'phone_2' => $contacto->TelemovelContacto,
                                'is_contact' => true,
                            ]
                        );

                        if (!empty($contacto->Morada)){
                            PrimaveraClientsContacts::updateOrCreate(
                                [
                                    'primavera_id' => $customer->Cliente,
                                    'morada' => $contacto->Morada
                                ],
                                [
                                    'cod_postal' => $contacto->CodPostal,
                                    'cod_postal_local' => $contacto->CodPostalLocal,
                                    'contacto' => $contacto->Contacto,
                                    'localidade' => $contacto->Localidade,
                                    'pais' => $contacto->Pais,
                                    'telefone' => $contacto->Telefone,
                                    'telefone_2' => $contacto->Telefone2,
                                    'telemovel' => $contacto->Telemovel,
                                    'email' => $contacto->Email,
                                    'primeiro_nome' => $contacto->PrimeiroNome,
                                    'ultimo_nome' => $contacto->UltimoNome,
                                    'zona' => $contacto->Zona,
                                    'descricao_zona' => $contacto->ZonaDescricao,
                                ]
                            );
                        }
                    }
                }

                $command->info('Address or contact saved for client: ' . $customer->Cliente);
            } catch (Throwable $th) {
                $command->error('Error to save Client address or contact' . $customer->Cliente);
            }
        }
    }

    /**
     * It takes a customer object, checks if the customer has a phone number, if it does, it splits the
     * phone number into an array, checks if the first element of the array is a valid phone number, if it
     * is, it adds it to the phones array, then it checks if the second element of the array is a valid
     * phone number, if it is, it adds it to the phones array, then it checks if the customer has a second
     * phone number, if it does, it adds it to the phones array, then it returns the phones array
     *
     * @param customer The customer object from the database
     *
     * @return An array of phone numbers.
     */
    public function sanitizePhone($customer)
    {
        $phones = array();

        if (isset($customer->Fac_Tel)) {
            $explode = explode("/", $customer->Fac_Tel);

            if (isset($explode[0])) {
                $phones["phone_1"] = intval(str_replace(' ', '', $explode[0]));
            }

            if (isset($explode[1])) {
                if (strlen(str_replace(' ', '', $explode[1])) >= 9) { //If the final string has 9 or more chars (Normal number does not have less than 9)
                    $phones["phone_3"] = intval(str_replace(' ', '', $explode[1]));
                }
            }
        }

        if (isset($customer->Telefone2)) {
            $phones["phone_2"] = intval(str_replace(' ', '', $customer->Telefone2));
        }

        return $phones;
    }

    public function sanitizeAddressPhoneNumber($phoneNumber)
    {
        $phoneNumber = str_replace(' ', '', $phoneNumber);

        if (strpos($phoneNumber, '/') !== false) {
            list($firstPart, $secondPart) = explode('/', $phoneNumber, 2);

            $firstPart = str_replace(' ', '', $firstPart);
            $firstPart = (int)preg_replace('/[^0-9]/', '', $firstPart);

            $secondPart = str_replace(' ', '', $secondPart);
            $secondPart = (int)preg_replace('/[^0-9]/', '', $secondPart);

            return [$firstPart, $secondPart];
        } else {
            $number = str_replace(' ', '', $phoneNumber);
            $number = (int)preg_replace('/[^0-9]/', '', $number);

            return [$number];
        }
    }
}
