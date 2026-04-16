<?php
    $hasWeighingView = hasPermission('Weighing', ['view', 'create', 'edit']);
    $hasAccountingView = hasPermission('Accounting', ['view', 'create', 'edit']);
    $hasStockView = hasPermission('Stock Management', ['view', 'create', 'edit']);
    $hasMasterDataView = hasPermission('Master Data', ['view', 'create', 'edit']);
    $hasReportView = hasPermission('Report', ['view', 'create', 'edit']);
    $hasUserManagementView = hasPermission('User Management', ['view', 'create', 'edit']);

    // Query Company links
    require_once "layouts/config.php";
    $companyId = '1';
    $stmtCompany = $link->prepare("SELECT links from Company where id = ?");
    mysqli_stmt_bind_param($stmtCompany, "s", $companyId);
    mysqli_stmt_execute($stmtCompany);
    mysqli_stmt_store_result($stmtCompany);
    mysqli_stmt_bind_result($stmtCompany, $companyLinks);
    
    $sopLink = null;
    $hardwareSetupLink = null;
    $helpLink = null;
    
    if (mysqli_stmt_fetch($stmtCompany)) {
        $linksArray = json_decode($companyLinks, true);
        $sopLink = $linksArray['sop_link'] ?? null;
        $hardwareSetupLink = $linksArray['hardware_setup_link'] ?? null;
        $helpLink = $linksArray['help_link'] ?? null;
    }
    mysqli_stmt_close($stmtCompany);
?>

<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.php" class="logo logo-dark">
            <span class="logo-sm">
                <img src="assets/images/logo/favicon.png" alt="" height="40">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo/logo1.png" alt="" height="110">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.php" class="logo logo-light">
            <span class="logo-sm">
                <img src="assets/images/logo/favicon.png" alt="" height="40">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo/logo1.png" alt="" height="110">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar" style="padding-bottom: 150px;">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span><?=$lang['t-menu']?></span></li>
                <li class="nav-item">
                    <a href="dashboard.php" class="nav-link"><b><i class="mdi mdi-view-dashboard"></i><?=$lang['t-dashboard']?></b></a>
                </li>

                <?php if($hasWeighingView): ?>
                <li class="nav-item">
                    <a href="index.php" class="nav-link"><b><i class="mdi mdi-weight"></i><?=$lang['t-weighing']?></b></a>
                </li>
                <?php endif; ?>

                
                <!-- <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button"
                        aria-expanded="true" aria-controls="sidebarDashboards">
                        <i class="ri-dashboard-2-line"></i> <span><?=$lang['t-weightweighing']?></span>
                    </a>
                    <div class="collapse show menu-dropdown" id="sidebarDashboards">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="weighing.php" class="nav-link"><?=$lang['t-weighing']?></a>
                            </li>
                        </ul>
                    </div>
                </li> -->

                <?php if($hasAccountingView): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarAccounting" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarAccounting">
                        <b><i class="ri-money-dollar-circle-line"></i> <span><?=$lang['t-accounting']?></span></b>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAccounting">
                        <ul class="nav nav-sm flex-column">
                            <?php if(hasModulePermission('Accounting', 'Sales Order (SO)', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="salesOrder.php" class="nav-link"><b><?=$lang['t-so']?></b></a>
                            </li>
                            <?php endif; ?>

                            <?php if(hasModulePermission('Accounting', 'Delivery Order (DO)', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="deliveryOrder.php" class="nav-link"><b><?=$lang['t-do']?></b></a>
                            </li>
                            <?php endif; ?>

                            <?php if(hasModulePermission('Accounting', 'Purchase Order (PO)', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="purchaseOrder.php" class="nav-link"><b><?=$lang['t-po']?></b></a>
                            </li>
                            <?php endif; ?>

                            <?php if(hasModulePermission('Accounting', 'Goods Received (GR)', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="goodsReceived.php" class="nav-link"><b><?=$lang['t-gr']?></b></a>
                            </li>
                            <?php endif; ?>

                            <?php if(hasModulePermission('Accounting', 'Stock Take', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="stockUsed.php" class="nav-link"><b><?=$lang['t-stockUsed']?></b></a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>

                <?php if($hasStockView): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarStock" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarStock">
                        <b><i class="ri-stock-line"></i> <span><?=$lang['t-stock']?></span></b>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarStock">
                        <ul class="nav nav-sm flex-column">
                            <?php if(hasModulePermission('Stock Management', 'Stock Take', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="bitumen.php" class="nav-link"><b><?=$lang['t-bitumen']?></b></a>
                            </li>
                            <?php endif; ?>

                            <?php if(hasModulePermission('Stock Management', 'Stock Take Log', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="stockTakeLog.php" class="nav-link"><b><?=$lang['t-stockTakeLog']?></b></a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(hasModulePermission('Stock Management', 'Inventory', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="inventory.php" class="nav-link"><b><?=$lang['t-inventory']?></b></a>
                            </li>
                            <?php endif; ?>
                             
                            <?php if(hasModulePermission('Stock Management', 'Asset Management', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="assetManagement.php" class="nav-link"><b><?=$lang['t-assetManagement']?></b></a>
                            </li><?php endif; ?>
                            
                            <?php if(hasModulePermission('Stock Management', 'Calculation Setup', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="calculationSetup.php" class="nav-link"><b><?=$lang['t-calculationSetup']?></b></a>
                            </li>
                            <?php endif; ?>       
                        </ul>
                    </div>
                </li> 
                <?php endif; ?>

                <?php if($hasMasterDataView): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarMasterdata" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarMasterdata">
                        <b><i class="ri-database-2-line"></i> <span><?=$lang['t-masterdata']?></span></b>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarMasterdata">
                        <ul class="nav nav-sm flex-column">
                            <?php if(hasModulePermission('Master Data', 'Customer', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="customer.php" class="nav-link"><b><?=$lang['t-customer']?></b></a>
                            </li>
                            <?php endif; ?>

                            <?php if(hasModulePermission('Master Data', 'Destination', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="destination.php" class="nav-link"><b><?=$lang['t-destination']?></b></a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(hasModulePermission('Master Data', 'Product', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="product.php" class="nav-link"><b><?=$lang['t-product']?></b></a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(hasModulePermission('Master Data', 'Raw Material', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="rawMaterial.php" class="nav-link"><b><?=$lang['t-raw-mat']?></b></a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(hasModulePermission('Master Data', 'Supplier', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="supplier.php" class="nav-link"><b><?=$lang['t-supplier']?></b></a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(hasModulePermission('Master Data', 'Vehicle', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="vehicle.php" class="nav-link"><b><?=$lang['t-vehicle']?></b></a>
                            </li> 
                            <?php endif; ?>
                            
                            <?php if(hasModulePermission('Master Data', 'Sales Representative', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="agent.php" class="nav-link"><b><?=$lang['t-agent']?></b></a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(hasModulePermission('Master Data', 'Transporter', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="transporter.php" class="nav-link"><b><?=$lang['t-transporter']?></b></a>
                            </li>
                            <?php endif; ?>
                            
                            <?php if(hasModulePermission('Master Data', 'Plant', ['view', 'create', 'edit'])): ?>
                            <li class="nav-item">
                                <a href="plant.php" class="nav-link"><b><?=$lang['t-plant']?></b></a>
                            </li>
                            <?php endif; ?>
                            
                            <!--li class="nav-item">
                                <a href="unit.php" class="nav-link"><b><?=$lang['t-unit']?></b></a>
                            </li>                                   
                            <li class="nav-item">
                                <a href="site.php" class="nav-link"><b><?=$lang['t-site']?></b></a>
                            </li-->   
                            <?php if(hasModulePermission('Master Data', 'Reason', ['view', 'create', 'edit'])): ?>                
                            <li class="nav-item">
                                <a href="reason.php" class="nav-link"><b><?=$lang['t-reason']?></b></a>
                            </li>  
                            <?php endif; ?>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>

                <?php if($hasReportView): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarReport" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarReport">
                        <b><i class="ri-file-chart-line"></i> <span><?=$lang['t-report']?></span></b>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarReport">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <!-- <li class="nav-item">
                                    <a href="weighingReport.php" class="nav-link"><?=$lang['t-weighingReport']?></a>
                                </li> -->
                                <?php if(hasModulePermission('Report', 'Sales', ['view', 'create', 'edit'])): ?>
                                <li class="nav-item">
                                    <a href="salesReport.php" class="nav-link"><b><?=$lang['t-soReport']?></b></a>
                                </li>
                                <?php endif; ?>

                                <?php if(hasModulePermission('Report', 'Purchase', ['view', 'create', 'edit'])): ?>
                                <li class="nav-item">
                                    <a href="purchaseReport.php" class="nav-link"><b><?=$lang['t-poReport']?></b></a>
                                </li>
                                <?php endif; ?>

                                <?php if(hasModulePermission('Report', 'Public', ['view', 'create', 'edit'])): ?>
                                <li class="nav-item">
                                    <a href="publicReport.php" class="nav-link"><b><?=$lang['t-publicReport']?></b></a>
                                </li>
                                <?php endif; ?>

                                <?php if(hasModulePermission('Report', 'Audit Log', ['view', 'create', 'edit'])): ?>
                                <li class="nav-item">
                                    <a href="auditLog.php" class="nav-link"><b><?=$lang['t-auditLog']?></b></a>
                                </li>
                                <?php endif; ?>

                                <?php if(hasModulePermission('Report', 'Api Log', ['view', 'create', 'edit'])): ?>
                                <li class="nav-item">
                                    <a href="apiLog.php" class="nav-link"><b><?=$lang['t-apiLog']?></b></a>
                                </li>  
                                <?php endif; ?>                       
                            </li>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>

                <?php if($hasUserManagementView): ?>
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarUserManagement" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarUserManagement">
                        <b><i class="ri-group-line"></i> <span><?=$lang['t-userManagement']?></span></b>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarUserManagement">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <?php if(hasModulePermission('User Management', 'User Setup', ['view', 'create', 'edit'])): ?>
                                <li class="nav-item">
                                    <a href="user.php" class="nav-link"><b><?=$lang['t-userSetup']?></b></a>
                                </li>
                                <?php endif; ?>

                                <?php if(hasModulePermission('User Management', 'Role', ['view', 'create', 'edit'])): ?>
                                <li class="nav-item">
                                    <a href="roles.php" class="nav-link"><b><?=$lang['t-roles']?></b></a>
                                </li> 
                                <?php endif; ?>

                                <?php if(hasModulePermission('User Management', 'Module', ['view', 'create', 'edit'])): ?>
                                <li class="nav-item">
                                    <a href="modules.php" class="nav-link"><b><?=$lang['t-modules']?></b></a>
                                </li>
                                <?php endif; ?>              
                            </li>
                        </ul>
                    </div>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarAuth" data-bs-toggle="collapse" role="button"
                        aria-expanded="false" aria-controls="sidebarAuth">
                        <b><i class="ri-settings-3-line"></i> <span><?=$lang['t-setting']?></span></b>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarAuth">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <?php if(hasModulePermission('Setting', 'Cronjob Setup', ['view', 'create', 'edit'])): ?>
                                <li class="nav-item">
                                    <a href="cronjobSetup.php" class="nav-link"><b><?=$lang['t-cronjobSetup']?></b></a>
                                </li> 
                                <?php endif; ?>

                                <?php if(hasModulePermission('Setting', 'Company Profile', ['view', 'create', 'edit'])): ?>
                                <li class="nav-item">
                                    <a href="companyProfile.php" class="nav-link"><b><?=$lang['t-companyProfile']?></b></a>
                                </li>
                                <?php endif; ?>
                                
                                <?php if(hasModulePermission('Setting', 'Port Setup', ['view', 'create', 'edit'])): ?>
                                <li class="nav-item">
                                    <a href="portSetup.php" class="nav-link"><b><?=$lang['t-portSetup']?></b></a>
                                </li> 
                                <?php endif; ?>

                                <li class="nav-item">
                                    <a href="myProfile.php" class="nav-link"><b><?=$lang['t-myProfile']?></b></a>
                                </li> 

                                <li class="nav-item">
                                    <a href="ChangePassword.php" class="nav-link"><b><?=$lang['t-changePassword']?></b></a>
                                </li>
                            </li>
                        </ul>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="php/logout.php" class="nav-link"><b><i class="mdi mdi-logout-variant"></i> <span><?=$lang['t-logout']?></span></b></a>
                </li>                 
            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <!-- Bottom Tabs -->
    <div class="sidebar-bottom-tabs" style="position: absolute; bottom: 0; left: 0; right: 0; background: inherit; border-top: 1px solid rgba(255,255,255,0.1); padding: 10px 0;">
        <div class="container-fluid">
            <ul class="navbar-nav">
                <?php if(!empty($sopLink)) {?>
                    <li class="nav-item">
                        <a href="<?=$sopLink?>" target="_blank" class="nav-link"><b><i class="ri-file-list-line"></i> <span>SOP</span></b></a>
                    </li>
                <?php } ?>
                <?php if(!empty($hardwareSetupLink)) {?>
                <li class="nav-item">
                    <a href="<?=$hardwareSetupLink?>" target="_blank" class="nav-link"><b><i class="ri-tools-line"></i> <span>Hardware Setup</span></b></a>
                </li>
                <?php } ?>
                <?php if(!empty($helpLink)) {?>
                <li class="nav-item">
                    <a href="<?=$helpLink?>" target="_blank" class="nav-link"><b><i class="ri-question-line"></i> <span>Help</span></b></a>
                </li>
                <?php } ?>
            </ul>
        </div>
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
