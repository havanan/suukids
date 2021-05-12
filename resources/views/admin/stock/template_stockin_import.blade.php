@foreach ($products as $item)
  <tr class="product-item">
      <input type="hidden" name="product_id[]" value="{{ $item['product_id'] }}" class="product-id">
      <input type="hidden" name="product_unit_id[]" value="{{ $item['unit_id'] }}" class="product-unit-id">
      <td>
          <input type="text" name="product_code[]" value="{{ $item['product_code'] }}" class="form-control product-code">
      </td>
      <td>
          <input type="text" class="form-control product-name" value="{{ $item['product_name'] }}" name="product_name[]">
      </td>
      <td>
          <input type="number" class="form-control product-quantity" value="{{ $item['quantity'] }}" name="product_quantity[]" min="0">
      </td>
      <td>
          <input type="text" name="product_expired_date[]" value="" class="form-control product-expired-date hasDatepicker">
      </td>
      <td>
          <input type="text" name="product_unit_name[]" value="{{ $item['unit_name'] }}" class="form-control product-unit-name" readonly="">
      </td>
      <td>
          <input type="number" name="product_price[]" value="{{ $item['price'] }}" min="0" class="form-control product-price">
      </td>
      <td>
          <input type="text" name="product_total[]" value="{{ $item['price'] * $item['quantity'] }}" min="0" class="form-control product-total" readonly="">
      </td>
      <td>
          <select class="form-control product-stock-group" name="product_stock_group_id[]">
              @foreach ($stockGroups as $idWarehouse => $warehouse)
                  <option value="{{ $idWarehouse }}" >{{ $warehouse }}</option>
              @endforeach
          </select>
      </td>
      <td>
          <button type="button" class="btn btn-danger btn-remove-product-item"><i class="fa fa-trash-alt"></i></button>
      </td>
  </tr>
@endforeach