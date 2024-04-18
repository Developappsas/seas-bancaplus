var raw = JSON.stringify({
    "credito_Id": document.getElementById(''),
    "cliente_Nombres": "LUISA JUDITH",
    "cliente_Apellidos": "BORRERO BARRIOS",
    "credito_Libranza": "FIAN 42561",
    "cliente_Identificacion": "33338389",
    "credito_Pagaduria": 10,
    "credito_Valor": 47395589,
    "credito_Valor_Cuota": 852000,
    "credito_Plazo": 168,
    "credito_Valor_Menos_Retanqueo": 42871680,
    "credito_Compras_Cartera": [
        {
            "compra_Entidad_Nombre": "SIMULTANEA ",
            "compra_Valor_Pagar": 31371630,
            "compra_Valor_Cuota": 579643,
            "compra_Fecha_Vencimiento": "2023-01-02"
        },
        {
            "compra_Entidad_Nombre": "kredit",
            "compra_Valor_Pagar": 0,
            "compra_Valor_Cuota": 60000
        },
        {
            "compra_Entidad_Nombre": "kredit",
            "compra_Valor_Pagar": 0,
            "compra_Valor_Cuota": 100000
        }
    ]
});

var requestOptions = {
    method: 'POST',    
    body: raw,
    redirect: 'follow'
};

fetch("https://az-ase-use2-prd-exp-back-inc-k.azurewebsites.net/api/Creditos/Crear_Creditos", requestOptions)
    .then(response => response.text())
    .then(result => console.log(result))
    .catch(error => console.log('error', error));