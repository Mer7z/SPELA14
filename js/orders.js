$(document).ready( function () {
  var table = $('#orders-table').DataTable(
    {
      order: [[6, 'desc']],
      rowReorder: {
        selector: 'td:nth-child(3)'
      },
      responsive: true
    }
  );
  var lastRefresh
  var notification = new Audio('audio/notification.mp3')
  refreshTable()

  
  // Warn if overriding existing method
if(Array.prototype.equals)
console.warn("Overriding existing Array.prototype.equals. Possible causes: New API defines the method, there's a framework conflict or you've got double inclusions in your code.");
// attach the .equals method to Array's prototype to call it on any array
Array.prototype.equals = function (array) {
// if the other array is a falsy value, return
if (!array)
    return false;

// compare lengths - can save a lot of time 
if (this.length != array.length)
    return false;

for (var i = 0, l=this.length; i < l; i++) {
    // Check if we have nested arrays
    if (this[i] instanceof Array && array[i] instanceof Array) {
        // recurse into the nested arrays
        if (!this[i].equals(array[i]))
            return false;       
    }           
    else if (this[i] != array[i]) { 
        // Warning - two different object instances will never be equal: {x:20} != {x:20}
        return false;   
    }           
}       
return true;
}
// Hide method from for-in loops
Object.defineProperty(Array.prototype, "equals", {enumerable: false});



  async function refreshTable(){
    const show = await showOrders()
    setTimeout(refreshTable, 5000)
  }

  async function showOrders(){
    $.get("get-orders.php",
      function (data) {
        let orders = data
        let rows = groupOrders(orders)
        let rowsData = [];
        rows.forEach(row => {
          let productos = 
          `<table>
            <tbody>
          `
          let cant = 
          `<table>
            <tbody>
          `
          let info = 
          `<table>
            <tbody>
          `
          let productId = ''
          let clientId = ''

          row.forEach((element, index) => {
            let infoArr = Object.entries(element.info)
            let infoTemplate = ''
            infoArr.forEach(info=>{
              if(info[1])
              infoTemplate += info[0] + ' '
            })
            productos += `<tr><td>${element.producto}</td></tr>`
            cant += `<tr><td>${element.cantidad}</td></tr>`
            info += `<tr><td>${infoTemplate}</td></tr>`
            productId += `<input type="hidden" name=orderId[] value=${element.id}>`
            clientId = element.idCliente ? `<input type="hidden" name=clientId value=${element.idCliente}>` : `<input type="hidden" name=clientIdNoReg value=${element.idNoReg}>`
            if(index === row.length-1){
              productos += 
              ` </tbody>
              </table>`
              cant += 
              ` </tbody>
              </table>`
              info += 
              ` </tbody>
              </table>`
            }
          });
          let enviado
          if(row[0].enviado === '1'){
            enviado = '<input type="checkbox" name="sendOrder" checked="checked">'
          } else{
            enviado = '<input type="checkbox" name="sendOrder">'
          }
          rowsData.push([row[0].direccion, row[0].nombre + ' ' + row[0].apellido, productos, cant, info, row[0].telefono, row[0].fecha, '<form class="send-order-form">'+enviado + productId+ clientId +'</form>'])
        });
        

        if(!lastRefresh){
          lastRefresh = rowsData
          table.clear()
          table.rows.add(rowsData)
          table.draw()
        }
        if(lastRefresh.equals(rowsData)){

        } else{
          lastRefresh = rowsData
          table.clear()
          table.rows.add(rowsData)
          table.draw()
          notification.play()
        }

        $('.send-order-form').submit(function (e){
          e.preventDefault()
          let target = e.target;
          let inputs = target.children;
          let orderIds = [];
          let clientId;
          let clientIdNoReg;
          for(let input of inputs){
            if($(input).attr('name') === "orderId[]"){
              orderIds.push($(input).val());
            }
            if($(input).attr('name') === "clientId"){
              clientId = $(input).val();
            } else if($(input).attr('name') === "clientIdNoReg"){
              clientIdNoReg = $(input).val();
            }
          }
          let data = {
            sendOrder: true,
            orderId: orderIds,
            clientId: clientId ? clientId : undefined,
            clientIdNoReg: clientIdNoReg ? clientIdNoReg : undefined
          }
          console.log(data);
          $.post("send-orders.php", data, function (res){
            console.log('res', res);
          })
        })

        $('.send-order-form input[type=checkbox]').click(function (e){
          let target = e.target
          e.preventDefault();
          if($(target).attr('checked') !== "checked"){
            $(target).attr('checked', '');
            let form = $(target).parent();
            form.submit();
          }
        })
      },
      "json"
    );
  }
  

  function groupOrders(orders){
    let rows = [];
    let groupedOrders = [];
    let lastFecha;
    let lastId;
    orders.forEach((order, index)=>{
      if (!lastFecha && !lastId) {
        lastFecha = order.fecha;
        if (order.idNoReg) {
          lastId = order.idNoReg;
        } else if (order.idCliente) {
          lastId = order.idCliente;
        }

        groupedOrders.push(order);
      } else {
        if (lastFecha === order.fecha && lastId === order.idNoReg || lastFecha === order.fecha && lastId === order.idCliente) {
          groupedOrders.push(order);
          if (index === orders.length - 1) {
            rows.push(groupedOrders);
          }
        } else {
          rows.push(groupedOrders);
          lastFecha = order.fecha;
          if (order.idNoReg) {
            lastId = order.idNoReg;
          } else if (order.idCliente) {
            lastId = order.idCliente;
          }
          groupedOrders = [order];
          if (index === orders.length - 1) {
            rows.push(groupedOrders);
          }
        }
      }
    })

    return rows
  }

} );


