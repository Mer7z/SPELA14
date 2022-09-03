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



  function refreshTable(){
    showOrders().then(function () {
      console.log('refreshing...');
      setTimeout(refreshTable, 5000)
    })
  }

  async function showOrders(){
    $.get("get-orders.php",
      function (data) {
        let orders = data
        let rows = groupOrders(orders)
        console.log(rows)
        let rowsData = [];
        rows.forEach(row => {
          let productos = ''
          let cant = ''
          let info = ''
          row.forEach((element, index) => {
            let infoArr = Object.entries(element.info)
            let infoTemplate = ''
            infoArr.forEach(info=>{
              if(info[1])
              infoTemplate += info[0] + ' '
            })
            productos += element.producto
            cant += element.cantidad
            info += infoTemplate
            if(index !== row.length-1){
              productos += '<hr>'
              cant += '<hr>'
              info += '<hr>'
            }
          });
          rowsData.push([row[0].direccion, row[0].nombre + ' ' + row[0].apellido, productos, cant, info, row[0].telefono, row[0].fecha, row[0].enviado])
        });

        if(!lastRefresh){
          lastRefresh = rowsData
          table.clear()
          table.rows.add(rowsData)
          table.draw()
        }
        if(lastRefresh.equals(rowsData)){
          console.log('no Chages')
          return
        } else{
          lastRefresh = rowsData
          table.clear()
          table.rows.add(rowsData)
          table.draw()
          console.log('chaged')
        }
        
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


