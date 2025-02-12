function calculate() {
    let num1 = parseFloat(document.getElementById("num1").value);
    let num2 = parseFloat(document.getElementById("num2").value);
    let operation = document.getElementById("operation").value;
    let result = 0;


    switch (operation) {
        case 'sum':
            result = num1 + num2;
            break;
        case 'sub':
            result = num1 - num2;
            break;
        case 'mul':
            result = num1 * num2;
            break;
        case 'div':
            if (num2 == 0) {
                alert('No se puede dividir entre cero')
            } else {
                result = num1 / num2;
            }
            break;
        default:
            alert("La operacion no existe")
    }
    document.getElementById('result').innerText = result;
}

function edad() {
    let num1 = parseFloat(document.getElementById("num1").value);
    let result = "";

    if (num1 >= 18) {
        result = "Mayor";
    } else {
        result = "Menor";
    }
    document.getElementById('result').innerText = result;
}

function lista() {
    const estudiante1 = { nombre: "Gerson", apellido: "Chacon", nota: 87 };
    const estudiante2 = { nombre: "Esteban", apellido: "Quiros", nota: 95 };
    const estudiantes = [estudiante1, estudiante2];

    let listaHTML = "";
    let total = 0;
    let totalNotas = estudiantes.length;

    for (let item of estudiantes) {
        listaHTML += `Nombre: ${item.nombre}, Apellido: ${item.apellido}, Nota: ${item.nota}<br>`;
        total += item.nota;
    }

    let promedio = total / totalNotas;

    document.getElementById("listaEstudiantes").innerHTML = listaHTML;
    document.getElementById("sumatotal").innerHTML = promedio;
}

lista();