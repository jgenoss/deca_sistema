<?php
session_start();
require_once '../includes/config.php';

$_POST = json_decode(file_get_contents("php://input"), true);

require_once '../modelo/dbconnect.php';
require_once '../modelo/entrada.php';

$ent = new entrada();
$db = new dbconnect();

switch (@$_GET['op']) {
  case 'getClientes': {
      if (isset($_GET)) {
        $rtn = AllConsult($ent->getCliente());
        $A = array();
        foreach ($rtn as $row) {
          $A[] = array(
            'id' => $row->id_cliente,
            'nombre' => $row->empresa
          );
        }
        setJson($A);
      }
    }
    break;
  case 'getBodega': {
      if (isset($_GET)) {
        $rtn = AllConsult($ent->getBodega());
        $A = array();
        foreach ($rtn as $row) {
          $A[] = array(
            'id' => $row->id_bodega,
            'nombre' => $row->nombre
          );
        }
        setJson($A);
      }
    }
    break;
  case 'getentradas': {
      if (isset($_GET)) {

        $rtn = AllConsult($ent->getentrada());
        $A = array();
        $url = "fpdf/rp_entrada.php?id=";
        foreach ($rtn as $row) {
          //$button='<button id="view" value="'.$row->id_entrada.'" class="btn btn-sm btn-warning"><i class="fas fa-eye"></i></button>
          //<button id="edit" value="'.$row->id_entrada.'" class="btn btn-sm btn-primary"><i class="fas fa-edit"></i></button>
          //<a class="btn btn-sm btn-info" target="_blank" href="'.$url.$row->id_entrada.'" ><i class="fa-solid fa-print"></i></a>';

          $button = '<div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-cog"></i></button>
            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                <button id="view" value="' . $row->id_entrada . '" class="dropdown-item"><i class="fas fa-eye"></i> Ver</button>
                <button id="edit" value="' . $row->id_entrada . '" class="dropdown-item"><i class="fas fa-edit"></i> Editar</button>
                <a target="_blank" href="' . $url . $row->id_entrada . '" class="dropdown-item"><i class="fa-solid fa-print"></i> Imprimir</a>
            </div>
          </div>';

          $A[] = array(
            ($row->created_at) ? $button : $button,
            $row->empresa . " / " . $row->nombre,
            $row->referencia,
            $row->factura,
            $row->id_entrada,
            $ent->getUndCaj($row->id_entrada)['cantidad'],
            ($row->id_bodega == 52) ? $row->caj_purina : $ent->getUndCaj($row->id_entrada)['cajas'],
            $row->fecha_de_comprobante,
            $row->created_at,

          );
        }
        setJson(
          array(
            "sEcho" => 1,
            "iTotalRecords" => count($A),
            "iTotalDisplayRecords" => count($A),
            "data" => $A
          )
        );
      }
    }
    break;
  case 'getListDate': {
      if (isset($_GET)) {
        $rtn = AllConsult($ent->getListDate(array($_GET['f_start'], $_GET['f_end'])));
        $A = array();
        $url = "fpdf/rp_entrada.php?id=";
        foreach ($rtn as $row) {
          $button = '<button id="view" value="' . $row->id_entrada . '" class="btn btn-sm btn-warning"><i class="fas fa-eye"></i></button>
            <a class="btn btn-sm btn-info" target="_blank" href="' . $url . $row->id_entrada . '" ><i class="fa-solid fa-print"></i></a>';
          $A[] = array(
            ($row->created_at) ? $button : $button,
            $row->empresa . " / " . $row->nombre,
            $row->referencia,
            $row->factura,
            $row->id_entrada,
            $ent->getUndCaj($row->id_entrada)['cantidad'],
            ($row->id_bodega == 52) ? $row->caj_purina : $ent->getUndCaj($row->id_entrada)['cajas'],
            $row->fecha_de_comprobante,
            $row->created_at,
          );
        }
        setJson(
          array(
            "sEcho" => 1,
            "iTotalRecords" => count($A),
            "iTotalDisplayRecords" => count($A),
            "data" => $A
          )
        );
      }
    }
    break;
  case 'getInventario': {
      if (isset($_GET)) {
        $rtn = AllConsult($ent->getInventario($_GET['id']));
        $A = array();
        foreach ($rtn as $row) {
          $button = '<button codigo="' . $row->ean . '" nombre="' . $row->pNombre . '" id="prod" value="' . $row->id_producto . '" class="btn btn-sm btn-primary"><i class="fas fa-plus"></i></button>';
          $A[] = array(
            '0' => $button,
            '1' => '<span class="badge badge-primary">' . $row->codigo_1 . '</span> <span class="badge badge-info">' . $row->codigo_2 . '</span> <span class="badge badge-warning">' . $row->ean . '</span>',
            '2' => $row->pNombre
          );
        }
        setJson(
          array(
            "sEcho" => 1,
            "iTotalRecords" => count($A),
            "iTotalDisplayRecords" => count($A),
            "data" => $A
          )
        );
      }
    }
    break;
  case 'getProductId': {
      if (isset($_POST)) {
        $rtn = Consult($ent->getProductId($_POST['id']));
        $A = array(
          'id' => $rtn->id_inventario,
          'producto' => $rtn->pNombre,
          'codigo' => $rtn->codigo
        );
        setJson($A);
      }
    }
    break;
  case 'regentrada': {

      if (isset($_POST)) {
        $A = array(
          '0' => $_POST['id_cliente'],
          '1' => $_POST['id_bodega'],
          '2' => $_POST['referencia'],
          '3' => $_POST['factura'],
          '4' => $_POST['tipo_comprobante'],
          '5' => $_POST['fecha'],
          '6' => $_POST['serie'],
          '7' => $_POST['observacion'],
          '8' => $_POST['direccion'],
          '9' => $_POST['file'],
          '10' => $_POST['listp'],
          '11' => $_POST['cja_purina']
        );
        try {
          var_dump($_POST);
          $A[9] = $ent->ConvertFilePDF($A[9]);
          $ent->setentrada($A, $_SESSION['START'][1]);
          setMsg("Info", 'entrada Procesada con exito', "success");
        } catch (Exception $e) {
          setMsg("Error", $e->getMessage(), "error");
        }
      }
    }
    break;
  case 'editentrada': {

      if (isset($_POST)) {
        $val = array(
          '0' => $_POST['id_cliente'],
          '1' => $_POST['id_bodega'],
          '2' => $_POST['referencia'],
          '3' => $_POST['factura'],
          '4' => $_POST['tipo_comprobante'],
          '5' => $_POST['fecha'],
          '6' => $_POST['serie'],
          '7' => $_POST['observacion'],
          '8' => $_POST['direccion'],
          '9' => $_POST['file'],
          '10' => $_POST['listp'],
          '11' => isset($_POST['cja_purina']),
          '12' => $_POST['id_entrada']
        );
        $id_session = $_SESSION['START'][1];
        try {
          $query = $db->sql("UPDATE entrada SET referencia = '$val[2]', factura = '$val[3]', tipo_comprobante = '$val[4]', fecha_de_comprobante = '$val[5]', serie = '$val[6]', observacion = '$val[7]', direccion = '$val[8]', archivo = '$val[9]', caj_purina = '$val[11]', id_cliente = '$val[0]', id_bodega = '$val[1]' WHERE id_entrada ='$val[12]'");
          $last_id = $db->lastInsertId();

          if ($query) {
            // Eliminar los detalles de entrada existentes
            $detallesAntiguos = $db->AllConsult($db->sql("SELECT id_producto, cantidad FROM entrada_detalle WHERE id_entrada='$val[12]'"));
            $db->sql("DELETE FROM entrada_detalle WHERE id_entrada='$val[12]'");
            $db->sql("DELETE FROM inventario_detallado WHERE id_entrada='$val[12]'");
            $db->sql("DELETE FROM movimientos WHERE referencia='$val[2]' AND factura='$val[3]'");
            // Actualizar el inventario correspondiente
            foreach ($detallesAntiguos as $detalle) {
              $id = $detalle->id_producto;
              $cantidad = $detalle->cantidad;
              $rtn = $db->Consult($db->sql("SELECT * FROM inventario WHERE id_producto=" . $id));
              if ($rtn) {
                $db->sql("UPDATE inventario SET cantidad=cantidad-'$cantidad' WHERE id_producto=" . $id);
              }

            }
            // Insertar los nuevos detalles de entrada
            foreach ($val[10] as $detalle) {
              $id = $detalle['id'];
              $cantidad = $detalle['cantidad'];
              $fv = $detalle['fv'];
              $fecha_v = $detalle['fecha_v'];

              $db->sql("UPDATE inventario SET cantidad=cantidad+'$cantidad' WHERE id_producto=" . $id);
              if ($fv) {
                $db->sql("INSERT INTO inventario_detallado (id_entrada,id_producto,id_usuario,cantidad,id_serie,fv,fecha_ven)VALUES('$val[12]','$id','$id_session','$cantidad','$val[6]','$fv','$fecha_v')");
                $db->sql("INSERT INTO movimientos (fecha,tipo,cantidad,producto_id,referencia,factura,fv,fecha_vencimiento)VALUES('$val[5]','entrada','$cantidad','$id','$val[2]','$val[3]','$fv','$fecha_v')");
              } else {
                $db->sql("INSERT INTO inventario_detallado (id_entrada,id_producto,id_usuario,cantidad,id_serie,fv)VALUES('$val[12]','$id','$id_session','$cantidad','$val[6]','$fv')");
                $db->sql("INSERT INTO movimientos (fecha,tipo,cantidad,producto_id,referencia,factura,fv,fecha_vencimiento)VALUES('$val[5]','entrada','$cantidad','$id','$val[2]','$val[3]','0','0000-00-00')");
              }

              $query = $db->sql("INSERT INTO entrada_detalle(id_entrada,id_serie,id_producto,cantidad)VALUES('$val[12]','$val[6]','$id','$cantidad')");
            }
          }
          setMsg("Info", 'Cambios realizados con exito', "success");
        } catch (Exception $e) {
          setMsg("Error", $e->getMessage(), "error");
        }
      }
    }
    break;
  case 'valiDate': {
      if (isset($_POST)) {
        if ($_POST['cantidad'] <= 0) {
          setJson(
            array(
              'tittle' => 'ERROR',
              'message' => 'Por favor introdusca una cantidad valida',
              'type' => 'info',
              'max' => 0
            )
          );
        }
      }
    }
    break;
  case 'getConsecutivo': {
      if (isset($_GET)) {
        $rtn = Consult($db->sql("SELECT MAX(serie) as next  FROM entrada"));
        if ($rtn) {
          setJson(array('next' => $rtn->next + 1));
        } else {
          setJson(array('next' => 1));
        }
      }
    }
    break;
  case 'getentradaId': {
      if (isset($_POST['id'])) {
        $rtn = Consult($ent->getentradaId($_POST['id']));
        $L = array();
        $rtn0 = AllConsult($ent->getentradaDetallada($rtn->id_entrada));
        $total = 0;
        foreach ($rtn0 as $key) {
          $total += $key->cantidad;
          $L[] = array(
            'id' => $key->id_producto,
            'codigo' => $key->codigo_1,
            'nombre' => $key->nombre,
            'cantidad' => $key->cantidad,
            'fv' => $key->fv,
            'fecha_v' => $key->fecha_v,
            'umb' => $key->umb,
            'cj' => round($key->cantidad / $key->umb)
          );
        }
        $A = array(
          'total' => $total,
          'id_entrada' => $rtn->id_entrada,
          'id_cliente' => $rtn->id_cliente,
          'id_bodega' => $rtn->id_bodega,
          'referencia' => $rtn->referencia,
          'factura' => $rtn->factura,
          'tipo_comprobante' => $rtn->tipo_comprobante,
          'fecha' => $rtn->fecha_de_comprobante,
          'file' => $rtn->archivo,
          'serie' => $rtn->id_entrada,
          'observacion' => $rtn->observacion,
          'direccion' => $rtn->direccion,
          'listp' => $L
        );
        setJson($A);
      }
    }
    break;
  case 'uploadFile': {
      require_once('../spreadSheetReader/php-excel-reader/excel_reader2.php');
      require_once('../spreadSheetReader/SpreadsheetReader.php');
      if (isset($_REQUEST)) {
        $file = $_FILES['file'];
        $file_name = file_get_contents($file['tmp_name']);
        // $file2 = explode("\n", $file1);
        // $file3 = array_filter($file2);
        $targetPath = '../upload_files/' . $file['name'];
        move_uploaded_file($file['tmp_name'], $targetPath);
        $Reader = new SpreadsheetReader($targetPath);
        unlink('../upload_files/' . $targetPath);
        $sheetCount = count($Reader->sheets());
        $A = array();
        for ($i = 0; $i < $sheetCount; $i++) {
          $Reader->ChangeSheet($i);
          foreach ($Reader as $key => $row) {
            $A[] = array(
              'codigo' => (isset($row[0])) ? $row[0] : '',
              'cantidad' => (isset($row[1])) ? $row[1] : ''
            );
          }
        }
        try {
          foreach ($A as $key) {
            if (empty($key['codigo']) || empty($key['cantidad'])) {
              throw new Exception('formato mal estructurado');
              break;
            } elseif (!empty($key['codigo']) || !empty($key['cantidad'])) {
              $ean = $key['codigo'];
              $rtn = Consult($db->sql("SELECT * FROM producto WHERE ean='$ean'"));
              // var_dump($rtn);
              if ($rtn) {
                $L[] = array(
                  'id' => $rtn->id_producto,
                  'codigo' => $rtn->ean,
                  'nombre' => $rtn->nombre,
                  'fecha_v' => '',
                  'fv' => false,
                  'cantidad' => $key['cantidad']
                );
              }
            }
          }
          setJson(
            array(
              'tittle' => "Info",
              'message' => "Cargue Masivo Procesado con exito",
              'type' => "success",
              'listp' => $L,
            )
          );
        } catch (Exception $e) {
          setMsg("Error", $e->getMessage(), "error");
        }
      }
    }
    break;
  default:
    die("NULL");
    break;
}