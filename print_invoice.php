<?php
//print_invoice.php
error_reporting(0);
 
 
 
if(isset($_GET["pdf"]) && isset($_GET["id"]))
{
 require_once 'pdf.php';
 include('database_connection.php');
 $output = '';
 $statement = $connect->prepare("
  SELECT * FROM tbl_order 
  WHERE order_id = :order_id
  LIMIT 1
 ");
 

 $statement->execute(
  array(
   ':order_id'       =>  $_GET["id"]
  )
 );
 $result = $statement->fetchAll();
 
   
 foreach($result as $row)
 {
	 $newdate=date('d-m-Y', strtotime(str_replace('.', '-', $row["order_date"])));
  
  $output .= '
   <table width="100%" border="1" cellpadding="5" cellspacing="0">
    <tr>
     <td colspan="2" align="center" style="font-size:18px"><b>Cash Bill</b></td>
    </tr>
	<tr>
     <td colspan="2"  style="font-size:14px";><b>Name<br>Address1<br>Address2<br>Email<br>Cell No: 123456789</b><br><span style="float:right;">B. Licence ABCD1234</span><br><span style="float:right;">E.B. Licence No.EFGH5678</span><br></td>
    </tr>
    <tr>
     <td colspan="2">
      <table width="100%" cellpadding="5">
       <tr>
        <td width="65%">
         To,<br />
         <!--<b>RECEIVER (BILL TO)</b><br />-->
         '.$row["order_receiver_name"].'<br /> 
          '.$row["order_receiver_address"].'<br />
        </td>
        <td width="35%">
         <br />
         Invoice No. : '.$row["order_no"].'<br />
		
         Date : '.$newdate.'<br />
        </td>
       </tr>
      </table>
      <br />
      <table width="100%" border="1" cellpadding="5" cellspacing="0">
       <tr>
        <th align="center" width="10%">Sr No.</th>
        <th align="center" width="70%">Item Name</th>
        <th align="center" width="10%">Qty</th>
        <th align="center" width="30%">Price</th>
        <!--<th>Actual Amt.</th>
        <th >Tax1 (%)</th>
        <th >Tax2 (%)</th>
        <th >Tax3 (%)</th>-->
		
        <th align="center" style="border-left:0px solid;">Total<span style="float:right;">Rs&nbsp;<b>|</b>&nbsp;Ps</span><br><br></th>
       </tr>
       <!--<tr>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th></th>
        <th>Rate</th>
        <th>Amt.</th>
        <th>Rate</th>
        <th>Amt.</th>
        <th>Rate</th>
        <th>Amt.</th>
       </tr>-->';
  $statement = $connect->prepare(
   "SELECT * FROM tbl_order_item 
   WHERE order_id = :order_id"
  );
  $statement->execute(
   array(
    ':order_id'       =>  $_GET["id"]
   )
  );
  
 
 //<td>'.$sub_row["order_item_actual_amount"].'</td>
   // <td>'.$sub_row["order_item_tax1_rate"].'</td>
  //  <td>'.$sub_row["order_item_tax1_amount"].'</td>
   // <td>'.$sub_row["order_item_tax2_rate"].'</td>
   // <td>'.$sub_row["order_item_tax2_amount"].'</td>
  //  <td>'.$sub_row["order_item_tax3_rate"].'</td>
   // <td>'.$sub_row["order_item_tax3_amount"].'</td>
	
	
  $item_result = $statement->fetchAll();
  $count = 0;
  foreach($item_result as $sub_row)
  {
   $count++;
   $output .= '
   <tr>
    <td align="center" style="border:none;border-left:1px solid black;border-right:1px solid black;" width="10%">'.$count.'</td>
    <td align="center" style="white-space: nowrap;border:none;border-right:1px solid black;" width="70%">'.$sub_row["item_name"].'</td>
    <td align="center" style="white-space: nowrap;border:none;border-right:1px solid black;" width="10%">'.round($sub_row["order_item_quantity"]).'</td>
    <td  align="center"style="white-space: nowrap;border:none;border-right:1px solid black;" width="30%">'.$sub_row["order_item_price"].'</td>  
    <td align="right" style="white-space: nowrap;border:none;border-right:1px solid black;border-right:1px solid black;">'.$sub_row["order_item_final_amount"].'</td>
   </tr>
   ';
  }
  $number=$row["order_total_after_tax"];
  $no = floor($number);
   $point = round($number - $no, 2) * 100;
   $hundred = null;
   $digits_1 = strlen($no);
   $i = 0;
   $str = array();
   $words = array('0' => '', '1' => 'one', '2' => 'two',
    '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six',
    '7' => 'seven', '8' => 'eight', '9' => 'nine',
    '10' => 'ten', '11' => 'eleven', '12' => 'twelve',
    '13' => 'thirteen', '14' => 'fourteen',
    '15' => 'fifteen', '16' => 'sixteen', '17' => 'seventeen',
    '18' => 'eighteen', '19' =>'nineteen', '20' => 'twenty',
    '30' => 'thirty', '40' => 'forty', '50' => 'fifty',
    '60' => 'sixty', '70' => 'seventy',
    '80' => 'eighty', '90' => 'ninety');
   $digits = array('', 'hundred', 'thousand', 'lakh', 'crore');
   while ($i < $digits_1) {
     $divider = ($i == 2) ? 10 : 100;
     $number = floor($no % $divider);
     $no = floor($no / $divider);
     $i += ($divider == 10) ? 1 : 2;
     if ($number) {
        $plural = (($counter = count($str)) && $number > 9) ? 's' : null;
        $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
        $str [] = ($number < 21) ? $words[$number] .
            " " . $digits[$counter] . $plural . " " . $hundred
            :
            $words[floor($number / 10) * 10]
            . " " . $words[$number % 10] . " "
            . $digits[$counter] . $plural . " " . $hundred;
     } else $str[] = null;
  }
  $str = array_reverse($str);
  $result = implode('', $str);
  $points = ($point) ?
    "." . $words[$point / 10] . " " . 
          $words[$point = $point % 10] : '';
  $word=ucwords($result . "Rupees  " . $points ). " Only";
 
  $output .= '
  
  <tr>
   <td align="right" style="border-top:1px solid black;border-right:none;"colspan="2"><b>Total</b></td>
   <td align="right" style="border:none;border-top:1px solid black;border-bottom:1px solid black;"></td>
   <td align="right" style="border:none;border-top:1px solid black;border-bottom:1px solid black;"></td>
   <td align="right" style="border-top:1px solid black;border-left:1px solid black;"><b>'.$row["order_total_after_tax"].'</b></td>
  </tr>
   
     </td>
    </tr>
	
   
   <table width="100%" style="">
   
  <tr>
   <td  align="right" style="border-right:0px;white-space: nowrap;"><b>Amount in Words</b></td>
   <td  align="right" style="font-size:14px;white-space: nowrap;"><b>'.$word.'</b></td>-->
  </tr>
  </table>
 <!-- <tr>
   <td colspan="11"><b>Total Amt. Before Tax :</b></td>
   <td align="right">'.$row["order_total_before_tax"].'</td>
  </tr>
  <tr>
   <td colspan="11">Add : Tax1 :</td>
   <td align="right">'.$row["order_total_tax1"].'</td>
  </tr>
  <tr>
   <td colspan="11">Add : Tax2 :</td>
   <td align="right">'.$row["order_total_tax2"].'</td>
  </tr>
  <tr>
   <td colspan="11">Add : Tax3 :</td>
   <td align="right">'.$row["order_total_tax3"].'</td>
  </tr>
  <tr>
   <td colspan="11"><b>Total Tax Amt.  :</b></td>
   <td align="right">'.$row["order_total_tax"].'</td>
  </tr>
  <tr>
   <td colspan="11"><b>Total Amt. After Tax :</b></td>
   <td align="right">'.$row["order_total_after_tax"].'</td>
  </tr>-->
  
  ';
  $output .= '
  </table>
	
  <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:20px;border-top:1px solid black;">
	
	<tr>
		<td width="100%" style="text-align:right;">Name, Electrical Contractor<br><br><br></td>
	</tr>
	<tr>
		<td width="100%" style="text-align:right;padding-right:70px;">Signature</td>
	</tr>
  </table>
     </table> 
  ';
 }
 $pdf = new Pdf();
 $file_name = 'Invoice-'.$row["order_no"].'.pdf';
 $pdf->loadHtml($output);
 $pdf->render();
 $pdf->stream($file_name, array("Attachment" => false));
}
?>