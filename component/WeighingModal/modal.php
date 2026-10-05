<?php
/*
 * Weighing Modal (#addModal) and the modals used by its submit flow:
 * #manualWeightReasonModal, #editReasonModal, #confirmDialogModal, #bypassModal, #cancelModal.
 * Shared by index.php, deliveryOrder.php and goodsReceived.php.
 *
 * Include inside the page content. Uses these result sets prepared by the page
 * (each is rewound before looping, so the page may have read them already):
 * $vehicles, $vehicles2, $customer, $supplier, $product, $rawMaterial, $transporter, $destination,
 * $agent, $site, $plant, $salesOrder, $purchaseOrder, $reasons.
 * JavaScript lives in script.php.
 */
?>
                                    <div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Add New Entry</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form role="form" id="weightForm" class="needs-validation" novalidate autocomplete="off">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="hstack gap-2 justify-content-center">
                                                                    <div class="col-xl-12 col-md-12 col-md-12">
                                                                        <div class="card bg-danger">
                                                                            <div class="card-body">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h2 class="mt-4 ff-secondary fw-semibold display-3 text-white"><span class="counter-value" id="indicatorWeight">0</span> KG</h2>
                                                                                    </div>
                                                                                    <!--div class="connected-align">
                                                                                        <div class="input-group-text color-palette" id="indicatorConnected"><i>Indicator Connected</i></div>
                                                                                        <div class="input-group-text bg-danger color-palette" id="checkingConnection"><i>Checking Connection</i></div>
                                                                                    </div-->
                                                                                    <div>
                                                                                        <div class="avatar-sm flex-shrink-0">
                                                                                            <span class="avatar-title bg-soft-light rounded-circle fs-2">
                                                                                                <i class="mdi mdi-weight-kilogram"></i>
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div><!-- end card body -->
                                                                        </div> <!-- end card-->
                                                                    </div> <!-- end col-->
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
                                                                <div class="hstack gap-2 justify-content-center">
                                                                    <div class="col-xl-12 col-md-12 col-md-12">
                                                                        <div class="card bg-danger">
                                                                            <div class="card-body">
                                                                                <div class="d-flex justify-content-between">
                                                                                    <div>
                                                                                        <h2 class="mt-4 ff-secondary fw-semibold display-3 text-white"><span class="counter-value" id="currentWeight">0</span> KG</h2>
                                                                                    </div>
                                                                                    <div>
                                                                                        <div class="avatar-sm flex-shrink-0">
                                                                                            <span class="avatar-title bg-soft-light rounded-circle fs-2">
                                                                                                <i class="mdi mdi-weight-kilogram"></i>
                                                                                            </span>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div><!-- end card body -->
                                                                        </div> <!-- end card-->
                                                                    </div> <!-- end col-->
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="row col-12">
                                                            <div class="col-xxl-12 col-lg-12">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionId" class="col-sm-4 col-form-label">Transaction ID</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control input-readonly" id="transactionId" name="transactionId" placeholder="Transaction ID" readonly>                                                                                  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row mb-3">
                                                                                    <label for="vehiclePlateNo1" class="col-sm-4 col-form-label">Vehicle Plate No.</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <div class="input-group-text">
                                                                                                <input class="form-check-input mt-0" id="manualVehicle" name="manualVehicle" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                            </div>
                                                                                            <input type="text" class="form-control" id="vehicleNoTxt" name="vehicleNoTxt" placeholder="Vehicle Plate No" style="display:none" required>
                                                                                            <div class="col-10 index-vehicle">
                                                                                                <select class="form-select select2" id="vehiclePlateNo1" name="vehiclePlateNo1" required>
                                                                                                    <option selected="-">-</option>
                                                                                                    <?php mysqli_data_seek($vehicles, 0); while($row2=mysqli_fetch_assoc($vehicles)){ ?>
                                                                                                        <option value="<?=$row2['veh_number'] ?>"><?=$row2['veh_number'] ?></option>
                                                                                                    <?php } ?>
                                                                                                </select>
                                                                                                <input type="text" class="form-control" id="vehiclePlateNo1Edit" name="vehiclePlateNo1Edit" hidden>
                                                                                            </div>
                                                                                            <div class="invalid-feedback">
                                                                                                Please fill in the field.
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divOrderWeight">
                                                                                <div class="row">
                                                                                    <label for="orderWeight" class="col-sm-4 col-form-label">Order Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="orderWeightBasicUom" name="orderWeightBasicUom" placeholder="0" readonly>
                                                                                            <div class="input-group-text" id="orderWeightUnit">KG</div>
                                                                                            <input type="number" class="form-control input-readonly" id="orderWeight" name="orderWeight" placeholder="0" readonly>
                                                                                            <div class="input-group-text">KG</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divSupplierWeight" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="supplierWeight" class="col-sm-4 col-form-label">Supplier Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="supplierWeightBasicUom" name="supplierWeightBasicUom" placeholder="0">
                                                                                            <div class="input-group-text" id="supplierWeightUnit">KG</div>
                                                                                            <input type="number" class="form-control input-readonly" id="supplierWeight" name="supplierWeight" placeholder="0" readonly>
                                                                                            <div class="input-group-text">KG</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>  
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="weightType" class="col-sm-4 col-form-label">Weight Type</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="weightType" name="weightType" class="form-select select2">
                                                                                            <option selected>Normal</option>
                                                                                            <option>Container</option>
                                                                                        </select>   
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none">
                                                                                <div class="row">
                                                                                    <label for="containerNo" class="col-sm-4 col-form-label">Container No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="containerNo" name="containerNo" placeholder="Container No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divPurchaseOrder">
                                                                                <div class="row">
                                                                                    <label for="purchaseOrder" class="col-sm-4 col-form-label">Purchase Order</label>
                                                                                    <div class="col-sm-8" id="poSelect">
                                                                                        <select class="form-select js-choice select2" id="purchaseOrder" name="purchaseOrder" required>
                                                                                            <option value="Pending PO verification">Pending PO verification</option>
                                                                                            <?php mysqli_data_seek($purchaseOrder, 0); while($rowPO=mysqli_fetch_assoc($purchaseOrder)){ ?>
                                                                                                <option value="<?=$rowPO['po_no'] ?>"><?=$rowPO['po_no'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                        <!--input type="text" class="form-control" id="purchaseOrderEdit" name="purchaseOrderEdit" disabled style="display:none;"-->
                                                                                    </div>
                                                                                    <div class="col-sm-8" id="soSelect">
                                                                                        <select class="form-select js-choice select2" id="salesOrder" name="salesOrder" required>
                                                                                            <option selected="Pending SO verification">Pending SO verification</option>
                                                                                            <?php mysqli_data_seek($salesOrder, 0); while($rowSO=mysqli_fetch_assoc($salesOrder)){ ?>
                                                                                                <option value="<?=$rowSO['order_no'] ?>"><?=$rowSO['order_no'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                        <!--input type="text" class="form-control" id="salesOrderEdit" name="salesOrderEdit" disabled style="display:none;"-->
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divPoSupplyWeight">
                                                                                <div class="row">
                                                                                    <label for="poSupplyWeight" class="col-sm-4 col-form-label">P/O Supply Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="poSupplyWeight" name="poSupplyWeight" placeholder="P/O Supply Weight" readonly>
                                                                                            <div class="input-group-text" id="poSupplyWeightUnit">KG</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="customerType" class="col-sm-4 col-form-label">Customer Type</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="customerType" name="customerType" class="form-select select2">
                                                                                            <option>Cash</option>
                                                                                            <option selected>Normal</option>
                                                                                        </select>   
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row" id="productNameDisplay">
                                                                                    <label for="productName" class="col-sm-4 col-form-label">Product Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="productName" name="productName" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php mysqli_data_seek($product, 0); while($rowProduct=mysqli_fetch_assoc($product)){ ?>
                                                                                                <option 
                                                                                                    value="<?=$rowProduct['name'] ?>" 
                                                                                                    data-id="<?=$rowProduct['id'] ?>" 
                                                                                                    data-price="<?=$rowProduct['price'] ?>" 
                                                                                                    data-code="<?=$rowProduct['product_code'] ?>" 
                                                                                                    data-high="<?=$rowProduct['high'] ?>" 
                                                                                                    data-low="<?=$rowProduct['low'] ?>" 
                                                                                                    data-variance="<?=$rowProduct['variance'] ?>" 
                                                                                                    data-description="<?=$rowProduct['description'] ?>">
                                                                                                    <?=$rowProduct['product_code']. ' - ' .$rowProduct['name'] ?>
                                                                                                </option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                        
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row" id="rawMaterialDisplay" style="display:none;">
                                                                                    <label for="rawMaterialName" class="col-sm-4 col-form-label">Raw Material</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="rawMaterialName" name="rawMaterialName" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php mysqli_data_seek($rawMaterial, 0); while($rowRawMat=mysqli_fetch_assoc($rawMaterial)){ ?>
                                                                                                <option value="<?=$rowRawMat['name'] ?>" data-id="<?=$rowRawMat['id'] ?>" data-code="<?=$rowRawMat['raw_mat_code'] ?>"><?=$rowRawMat['raw_mat_code']. ' - ' .$rowRawMat['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>           
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divWeightDifference" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="weightDifference" class="col-sm-4 col-form-label">Weight Difference</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="weightDifference" name="weightDifference" placeholder="Weight Difference" readonly>
                                                                                            <div class="input-group-text">KG</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="reduceWeight" class="col-sm-4 col-form-label">Reduce Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="reduceWeight" name="reduceWeight" placeholder="0">
                                                                                            <div class="input-group-text">KG</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionStatus" class="col-sm-4 col-form-label">Transaction Status</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="transactionStatus" name="transactionStatus" class="form-select select2">
                                                                                            <option value="Sales" selected>Sales</option>
                                                                                            <option value="Purchase">Purchase</option>
                                                                                            <option value="Local">Public</option>
                                                                                            <option value="WIP">WIP</option>
                                                                                            <option value="Return">Return</option>
                                                                                        </select>  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divCustomerName">
                                                                                <div class="row">
                                                                                    <label for="customerName" class="col-sm-4 col-form-label">Customer Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select js-choice select2" id="customerName" name="customerName" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php mysqli_data_seek($customer, 0); while($rowCustomer=mysqli_fetch_assoc($customer)){ ?>
                                                                                                <option value="<?=$rowCustomer['name'] ?>" data-code="<?=$rowCustomer['customer_code'] ?>"><?=$rowCustomer['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="divSupplierName" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="supplierName" class="col-sm-4 col-form-label">Supplier Name</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="supplierName" name="supplierName" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php mysqli_data_seek($supplier, 0); while($rowSupplier=mysqli_fetch_assoc($supplier)){ ?>
                                                                                                <option value="<?=$rowSupplier['name'] ?>" data-code="<?=$rowSupplier['supplier_code'] ?>"><?=$rowSupplier['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="balance" class="col-sm-4 col-form-label">Balance</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="text" class="form-control input-readonly text-danger" id="balance" name="balance" placeholder="0" readonly>   
                                                                                            <div class="input-group-text">KG</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="row mt-2" id="insufficientBalDisplay" style="display:none;">
                                                                                    <span class="col-sm-4"></span>
                                                                                    <label class="col-sm-8 text-danger">Insufficient Balance</label>
                                                                                </div>
                                                                            </div> 
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transactionDate" class="col-sm-4 col-form-label">Transaction Date</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="date" class="form-control" data-provider="flatpickr" id="transactionDate" name="transactionDate" required>
                                                                                        <div class="invalid-feedback">
                                                                                            Please fill in the field.
                                                                                        </div>    
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="invoiceNo" class="col-sm-4 col-form-label">Invoice No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="invoiceNo" name="invoiceNo" placeholder="Invoice No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="exDel" class="col-sm-4 col-form-label">Ex-Quarry/Delivered</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="form-check align-radio mr-2">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="exDel" id="manualEx" value="true">
                                                                                            <label class="form-check-label" for="manualEx">
                                                                                               Ex-Quarry
                                                                                            </label>
                                                                                        </div>

                                                                                        <div class="form-check align-radio">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="exDel" id="manualDel" value="false" checked>
                                                                                            <label class="form-check-label" for="manualDel">
                                                                                               Delivered
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="unitPriceDisplay">
                                                                                <div class="row">
                                                                                    <label for="unitPrice" class="col-sm-4 col-form-label">Unit Price</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control" id="unitPrice" name="unitPrice" placeholder="0">
                                                                                            <div class="input-group-text">RM</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="agent" class="col-sm-4 col-form-label">Sales Representative</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="agent" name="agent" >
                                                                                            <option selected="-">-</option>
                                                                                            <?php mysqli_data_seek($agent, 0); while($rowAgent=mysqli_fetch_assoc($agent)){ ?>
                                                                                                <option value="<?=$rowAgent['name'] ?>" data-code="<?=$rowAgent['agent_code'] ?>"><?=$rowAgent['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                         
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="transporter" class="col-sm-4 col-form-label">Transporter</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="transporter" name="transporter" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php mysqli_data_seek($transporter, 0); while($rowTransporter=mysqli_fetch_assoc($transporter)){ ?>
                                                                                                <option value="<?=$rowTransporter['name'] ?>" data-code="<?=$rowTransporter['transporter_code'] ?>"><?=$rowTransporter['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>                                                                                          
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="sstDisplay">
                                                                                <div class="row">
                                                                                    <label for="sstPrice" class="col-sm-4 col-form-label">SST (-)</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="sstPrice" name="sstPrice" placeholder="0" readonly>
                                                                                            <div class="input-group-text">RM</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none;">
                                                                                <div class="row">
                                                                                    <label for="indicatorId" class="col-sm-4 col-form-label">Indicator ID</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="indicatorId" name="indicatorId" class="form-select select2" >
                                                                                            <option selected>ind12345</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3"  <?php 
                                                                                if(!hasPermission('Weighing', ['manual_weighing'])){
                                                                                    echo 'style="display:none;"';
                                                                                }?>>
                                                                                <div class="row">
                                                                                    <label for="manualWeight" class="col-sm-4 col-form-label">Manual Weight</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="form-check align-radio mr-2">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="manualWeight" id="manualWeightYes" value="true">
                                                                                            <label class="form-check-label" for="manualWeightYes">
                                                                                               Yes
                                                                                            </label>
                                                                                        </div>

                                                                                        <div class="form-check align-radio">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="manualWeight" id="manualWeightNo" value="false" checked>
                                                                                            <label class="form-check-label" for="manualWeightNo">
                                                                                               No
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="destination" class="col-sm-4 col-form-label">Destination</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="destination" name="destination" required>
                                                                                            <option selected="-">-</option>
                                                                                            <?php mysqli_data_seek($destination, 0); while($rowDestination=mysqli_fetch_assoc($destination)){ ?>
                                                                                                <option value="<?=$rowDestination['name'] ?>" data-code="<?=$rowDestination['destination_code'] ?>"><?=$rowDestination['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>            
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="subTotalPriceDisplay">
                                                                                <div class="row">
                                                                                    <label for="subTotalPrice" class="col-sm-4 col-form-label">Sub-Total Price</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="subTotalPrice" name="subTotalPrice" placeholder="0" readonly>
                                                                                            <div class="input-group-text">RM</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div> 
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="doDisplay">
                                                                                <div class="row">
                                                                                    <label for="deliveryNo" class="col-sm-4 col-form-label">Delivery No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="deliveryNo" name="deliveryNo" placeholder="Delivery No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" style="display:none">
                                                                                <div class="row">
                                                                                    <label for="loadDrum" class="col-sm-4 col-form-label">By-Load/By-Drum</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="form-check align-radio mr-2">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="loadDrum" id="manualLoad" value="true" checked>
                                                                                            <label class="form-check-label" for="manualLoad">
                                                                                               By-Load
                                                                                            </label>
                                                                                        </div>

                                                                                        <div class="form-check align-radio">
                                                                                            <input class="form-check-input radio-manual-weight" type="radio" name="loadDrum" id="manualDrum" value="false">
                                                                                            <label class="form-check-label" for="manualDrum">
                                                                                               By-Drum
                                                                                            </label>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="batchDrum" class="col-sm-4 col-form-label">By-Batch/By-Drum</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select id="batchDrum" name="batchDrum" class="form-select select2" required>
                                                                                            <option value="Batch">Batch</option>
                                                                                            <option value="Drum">Drum</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="totalPriceDisplay">
                                                                                <div class="row">
                                                                                    <label for="totalPrice" class="col-sm-4 col-form-label">Total Price</label>
                                                                                    <div class="col-sm-8">
                                                                                        <div class="input-group">
                                                                                            <input type="number" class="form-control input-readonly" id="totalPrice" name="totalPrice" placeholder="0" readonly>
                                                                                            <div class="input-group-text">RM</div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>  
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="plant" class="col-sm-4 col-form-label">Plant</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="plant" name="plant" required>
                                                                                            <?php mysqli_data_seek($plant, 0); while($rowPlant=mysqli_fetch_assoc($plant)){ ?>
                                                                                                <option value="<?=$rowPlant['name'] ?>" data-code="<?=$rowPlant['plant_code'] ?>" data-id="<?=$rowPlant['id'] ?>"><?=$rowPlant['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>        
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                                <div class="row">
                                                                                    <label for="siteName" class="col-sm-4 col-form-label">Project</label>
                                                                                    <div class="col-sm-8">
                                                                                        <select class="form-select select2" id="siteName" name="siteName">
                                                                                            <option selected="-">-</option>
                                                                                            <?php mysqli_data_seek($site, 0); while($rowSite=mysqli_fetch_assoc($site)){ ?>
                                                                                                <option value="<?=$rowSite['name'] ?>" data-code="<?=$rowSite['site_code'] ?>"><?=$rowSite['name'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>        
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="tinNoDisplay">
                                                                                <div class="row">
                                                                                    <label for="tinNo" class="col-sm-4 col-form-label">Tin No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="tinNo" name="tinNo" placeholder="Tin No">
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row">
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="idNoDisplay">
                                                                                <div class="row">
                                                                                    <label for="idNo" class="col-sm-4 col-form-label">Id No</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="idNo" name="idNo" placeholder="Id No">  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-xxl-4 col-lg-4 mb-3" id="idTypeDisplay">
                                                                                <div class="row">
                                                                                    <label for="idType" class="col-sm-4 col-form-label">Id Type</label>
                                                                                    <div class="col-sm-8">
                                                                                        <input type="text" class="form-control" id="idType" name="idType" placeholder="Id Type">  
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row col-12">
                                                            <div class="col-xxl-4 col-lg-4">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row mb-3" id="noOfDrumDisplay" style="display:none;">
                                                                            <label for="noOfDrum" class="col-sm-4 col-form-label">No of Drum</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="number" class="form-control" id="noOfDrum" name="noOfDrum">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncoming" class="col-sm-4 col-form-label">Incoming</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <!-- <div class="input-group-text">
                                                                                        <input class="form-check-input mt-0" id="manual" name="manual" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                    </div>                                                                                             -->
                                                                                    <input type="number" class="form-control input-readonly" id="grossIncoming" name="grossIncoming" placeholder="0" readonly>
                                                                                    <div class="input-group-text">KG</div>
                                                                                    <button class="input-group-text btn btn-danger fs-5" id="grossCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mb-3">
                                                                            <label for="grossIncomingDate" class="col-sm-4 col-form-label">Incoming Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate" name="grossIncomingDate">
                                                                            </div>
                                                                        </div>

                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoing" class="col-sm-4 col-form-label">Outgoing</label>
                                                                            <div class="col-sm-8">                                                                                     
                                                                                <div class="input-group">
                                                                                    <!-- <div class="input-group-text">
                                                                                        <input class="form-check-input mt-0" id="manualOutgoing" name="manualOutgoing" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                    </div>                                                                                                -->
                                                                                    <input type="number" class="form-control input-readonly" id="tareOutgoing" name="tareOutgoing" placeholder="0" readonly>
                                                                                    <div class="input-group-text">KG</div>
                                                                                    <button class="input-group-text btn btn-danger fs-5" id="tareCapture" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>                                                                                       
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoingDate" class="col-sm-4 col-form-label">Outgoing Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="tareOutgoingDate" name="tareOutgoingDate">
                                                                            </div>
                                                                        </div>                                                                        
                                                                        <div class="row mb-3">
                                                                            <label for="nettWeight" class="col-sm-4 col-form-label">Nett Weight</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="nettWeight" name="nettWeight" placeholder="0" readonly>
                                                                                    <div class="input-group-text">KG</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>                                                                                                                                  
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4" id="containerCard" style="display:none;">
                                                                <div class="card bg-light">
                                                                    <div class="card-body">
                                                                        <div class="row mb-3">
                                                                            <label for="vehiclePlateNo2" class="col-sm-4 col-form-label">Vehicle Plate No 2</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <div class="input-group-text">
                                                                                        <input class="form-check-input mt-0" id="manualVehicle2" name="manualVehicle2" type="checkbox" value="0" aria-label="Checkbox for following text input">
                                                                                    </div>
                                                                                    <input type="text" class="form-control" id="vehicleNoTxt2" name="vehicleNoTxt2" placeholder="Vehicle Plate No" style="display:none">
                                                                                    <div class="col-10 index-vehicle2">
                                                                                        <select class="form-select select2" id="vehiclePlateNo2" name="vehiclePlateNo2">
                                                                                            <option selected="-">-</option>
                                                                                            <?php mysqli_data_seek($vehicles2, 0); while($rowv2=mysqli_fetch_assoc($vehicles2)){ ?>
                                                                                                <option value="<?=$rowv2['veh_number'] ?>" data-weight="<?=$rowv2['vehicle_weight'] ?>"><?=$rowv2['veh_number'] ?></option>
                                                                                            <?php } ?>
                                                                                        </select>
                                                                                    </div>
                                                                                    <div class="invalid-feedback">
                                                                                        Please fill in the field.
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncoming2" class="col-sm-4 col-form-label">Incoming</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="grossIncoming2" name="grossIncoming2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">KG</div>
                                                                                    <button class="input-group-text btn btn-danger fs-5" id="grossCapture2" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="grossIncomingDate2" class="col-sm-4 col-form-label">Incoming Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" id="grossIncomingDate2" name="grossIncomingDate2">
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoing2" class="col-sm-4 col-form-label">Outgoing</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="tareOutgoing2" name="tareOutgoing2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">KG</div>
                                                                                    <button class="input-group-text btn btn-danger fs-5" id="tareCapture2" type="button"><i class="mdi mdi-sync"></i></button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="row mb-3">
                                                                            <label for="tareOutgoingDate2" class="col-sm-4 col-form-label">Outgoing Date</label>
                                                                            <div class="col-sm-8">
                                                                                <input type="text" class="form-control input-readonly" placeholder="" id="tareOutgoingDate2" name="tareOutgoingDate2">
                                                                            </div>
                                                                        </div>                                                                        
                                                                        <div class="row mb-3">
                                                                            <label for="nettWeight2" class="col-sm-4 col-form-label">Nett Weight</label>
                                                                            <div class="col-sm-8">
                                                                                <div class="input-group">
                                                                                    <input type="number" class="form-control input-readonly" id="nettWeight2" name="nettWeight2" placeholder="0" readonly>
                                                                                    <div class="input-group-text">KG</div>
                                                                                </div>
                                                                            </div>
                                                                        </div>                                                                    
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-xxl-4 col-lg-4 mb-3">
                                                                <div class="row">
                                                                    <label for="otherRemarks" class="col-sm-2 col-form-label">Other Remarks</label>
                                                                    <div class="col-sm-10">
                                                                        <textarea class="form-control" id="otherRemarks" name="otherRemarks" rows="3" placeholder="Other Remarks"></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="row mt-3" id="manualWeightReasonDisplay" style="display: none;">
                                                                    <label for="manualWeightReason" class="col-sm-2 col-form-label">Manual Weighing Reason</label>
                                                                    <div class="col-sm-10">
                                                                        <textarea class="form-control input-readonly" id="manualWeightReason" name="manualWeightReason" rows="3" placeholder="Manual Weighing Reason" readonly></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="row mt-3" id="editReasonDisplay" style="display: none;">
                                                                    <label for="editReason" class="col-sm-2 col-form-label">Edit Reason</label>
                                                                    <div class="col-sm-10">
                                                                        <textarea class="form-control input-readonly" id="editReason" rows="3" placeholder="Edit Reason" readonly></textarea>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-lg-12" id="closedSalesOrderWarning" style="display:none;">
                                                            <div class="alert alert-danger mb-3" role="alert">
                                                                <strong>The Sales Order close, please contact Admin</strong>
                                                            </div>
                                                        </div>

                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-danger" id="submitWeightPrint">Submit & Print</button>
                                                                <button type="button" class="btn btn-warning" id="submitWeightCancel">Submit & Cancel</button>
                                                                <button type="button" class="btn btn-primary" id="submitWeight">Submit</button>
                                                            </div>
                                                        </div><!--end col-->   
                                                        
                                                        <input type="hidden" id="bypassReason" name="bypassReason">
                                                        <input type="hidden" id="manualWeightReasonInput" name="manualWeightReasonInput">
                                                        <input type="hidden" id="editReasonInput" name="editReasonInput">
                                                        <input type="hidden" id="finalWeight" name="finalWeight">
                                                        <input type="hidden" id="customerCode" name="customerCode">
                                                        <input type="hidden" id="custName" name="custName">
                                                        <input type="hidden" id="destinationCode" name="destinationCode">
                                                        <input type="hidden" id="plantId" name="plantId">
                                                        <input type="hidden" id="plantCode" name="plantCode">
                                                        <input type="hidden" id="agentCode" name="agentCode">
                                                        <input type="hidden" id="status" name="status">
                                                        <input type="hidden" id="productId" name="productId">
                                                        <input type="hidden" id="productCode" name="productCode">
                                                        <input type="hidden" id="productDescription" name="productDescription">
                                                        <input type="hidden" id="productPrice" name="productPrice">
                                                        <input type="hidden" id="productHigh" name="productHigh">
                                                        <input type="hidden" id="productLow" name="productLow">
                                                        <input type="hidden" id="productVariance" name="productVariance">
                                                        <input type="hidden" id="transporterCode" name="transporterCode">
                                                        <input type="hidden" id="transporterName" name="transporterName">
                                                        <input type="hidden" id="supplierCode" name="supplierCode">
                                                        <input type="hidden" id="rawMaterialCode" name="rawMaterialCode">
                                                        <input type="hidden" id="rawMaterialId" name="rawMaterialId">
                                                        <input type="hidden" id="siteCode" name="siteCode">
                                                        <input type="hidden" id="id" name="id">  
                                                        <input type="hidden" id="weighbridge" name="weighbridge" value="Weigh1">
                                                        <input type="hidden" id="previousRecordsTag" name="previousRecordsTag">
                                                        <input type="hidden" id="basicNettWeight" name="basicNettWeight">
                                                    </form>
                                                </div>
                                            </div><!-- /.modal-content -->
                                        </div><!-- /.modal-dialog -->
                                    </div><!-- /.modal -->

                                    <div class="modal fade" id="manualWeightReasonModal" tabindex="-1" role="dialog" aria-labelledby="manualWeightReasonModalTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="manualWeightReasonModalTitle">Reason for Manual Weighing</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="manualWeightReasonPrompt" class="form-label">Reason for Manual Weighing</label>
                                                        <textarea class="form-control" id="manualWeightReasonPrompt" rows="4" placeholder="Enter reason"></textarea>
                                                        <span class="invalid-feedback" id="manualWeightReasonError"></span>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary" id="submitManualWeightReason">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="editReasonModal" tabindex="-1" role="dialog" aria-labelledby="editReasonModalTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editReasonModalTitle">Reason for Editing Completed Transaction</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label for="editReasonPrompt" class="form-label">Reason for Editing Completed Transaction</label>
                                                        <textarea class="form-control" id="editReasonPrompt" rows="4" placeholder="Enter reason"></textarea>
                                                        <span class="invalid-feedback" id="editReasonError"></span>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary" id="submitEditReason">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="confirmDialogModal" tabindex="-1" role="dialog" aria-labelledby="confirmDialogModalTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="confirmDialogModalTitle">Confirm Submit</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div id="confirmDialogMessage" style="white-space: pre-line;"></div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" id="confirmDialogCancel" data-bs-dismiss="modal">Close</button>
                                                    <button type="button" class="btn btn-primary" id="confirmDialogSubmit">Submit</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="bypassModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-scrollable custom-xxl">
                                            <div class="modal-content">
                                                <form role="form" id="bypassForm" class="needs-validation" novalidate autocomplete="off">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalScrollableTitle">Key in reasons</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="row mb-12">
                                                            <label for="nettWeight" class="col-sm-4 col-form-label">Password</label>
                                                            <div class="col-sm-8">
                                                                <div class="input-group">
                                                                    <input type="text" class="form-control" id="passcode" name="passcode" placeholder="0" required>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row col-xxl-12 col-lg-12 mb-12">
                                                            <div class="row">
                                                                <label for="reason" class="col-sm-2 col-form-label">Reasons *</label>
                                                                <div class="col-sm-10">
                                                                    <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Reasons" required></textarea>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                                <button type="button" class="btn btn-danger" id="submitBypass">Submit</button>
                                                            </div>
                                                        </div><!--end col-->   
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="modal fade" id="cancelModal">
                                        <div class="modal-dialog modal-xl" style="max-width: 90%;">
                                            <div class="modal-content">
                                                <form role="form" id="cancelForm">
                                                    <div class="modal-header bg-gray-dark color-palette">
                                                        <h4 class="modal-title">Cancellation</h4>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input type="hidden" class="form-control" id="id" name="id">
                                                        <div class="row mb-3">
                                                            <div class="form-group">
                                                                <label>Cancellation Reason *</label>
                                                                <select class="form-select select2" id="cancelId" name="cancelId" required>
                                                                    <?php mysqli_data_seek($reasons, 0); while($rowR=mysqli_fetch_assoc($reasons)){ ?>
                                                                        <option value="<?=$rowR['id'] ?>"><?=$rowR['reason'] ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group">
                                                                <label>Remarks / Other Reasons *</label>
                                                                <textarea class="form-control" id="cancelReason" name="cancelReason" rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer justify-content-between bg-gray-dark color-palette">
                                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-danger" id="submitCancel">Save changes</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
