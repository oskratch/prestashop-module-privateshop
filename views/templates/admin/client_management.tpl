<div class="panel col-md-12">
    <div class="panel-heading">
        Pendientes de aprobación			
    </div>    
    <div class="table-responsive-row clearfix">
        {if $customers|@count > 0}
            <table class="table">
                <thead>
                    <tr>
                        <th>{l s='Nombre'}</th>
                        <th>{l s='Apellidos'}</th>
                        <th>{l s='Email'}</th>
                        <th>{l s='Fecha'}</th>
                        <th>{l s='Acciones'}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$customers item=customer}
                        <tr data-customer-id="{$customer.customer_id}">
                            <td>{$customer.firstname}</td>
                            <td>{$customer.lastname}</td>
                            <td>{$customer.email}</td>
                            <td>{$customer.approved_at|date_format:'%d-%m-%Y %H:%M'}</td>
                            <td>
                                <a href="#" class="btn btn-success tooltip-link js-approve-customer-row-action" 
                                    data-customer-id="{$customer.customer_id}" 
                                    data-customer-approve-url="{$link->getAdminLink('AdminClientManagement')}&id_customer={$customer.customer_id}&approve=1">
                                    Aprobar
                                </a>
                                <a href="#" class="btn btn-danger tooltip-link js-delete-customer-row-action" 
                                    data-customer-id="{$customer.customer_id}" 
                                    data-customer-delete-url="{$link->getAdminLink('AdminClientManagement')}&id_customer={$customer.customer_id}&delete=1">
                                    Eliminar
                                </a>
                            </td>
                        </tr>
                    {/foreach}
                </tbody>
            </table>
            <div class="modal fade" id="alertModal" tabindex="-1" role="dialog" aria-labelledby="alertModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-body">
                        <h4 id="alertMessage">Message</h4>
                    </div>
                    </div>
                </div>
            </div>    
            <div class="modal fade" id="confirmModal" tabindex="-1" role="dialog" aria-labelledby="confirmModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="confirmModalLabel">Confirma la acción</h4>
                    </div>
                    <div class="modal-body">
                        <p id="confirmMessage">¿Estás seguro?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="confirmButton">Confirmar</button>
                    </div>
                    </div>
                </div>
            </div>
        {else}        
            <div class="alert alert-success">
                {l s='No hay clientes pendientes de revisión.'}
            </div>
        {/if}
    </div>
</div>
<div class="panel col-md-12">
    <div class="panel-heading">
        Opción de envío a domicilio			
    </div>
    <p class="pb-3">
        <small class="form-text">De forma predeterminada, la opción de envío a domicilio está deshabilitada para los clientes registrados.</small>
    </p>
    {if $all_customers|@count > 0}
        <table class="table">
            <thead>
                <tr>
                    <th>{l s='Nombre'}</th>
                    <th>{l s='Apellidos'}</th>
                    <th>{l s='Email'}</th>
                    <th>{l s='Envío a domicilio'}</th>
                </tr>
            </thead>
            <tbody>
                {foreach from=$all_customers item=customer}
                    <tr data-customer-id="{$customer.customer_id}">
                        <td>{$customer.firstname}</td>
                        <td>{$customer.lastname}</td>
                        <td>{$customer.email}</td>
                        <td>
                            <span class="switch prestashop-switch fixed-width-lg">
                                <input type="radio" 
                                    name="shipping_restriction_{$customer.customer_id}" 
                                    id="shipping_restriction_on_{$customer.customer_id}_1" 
                                    value="1" 
                                    {if $customer.shipping_restriction == 1}checked{/if} 
                                    class="shipping-restriction-toggle" 
                                    data-customer-id="{$customer.customer_id}">
                                <label for="shipping_restriction_on_{$customer.customer_id}_1" class="radioCheck">Sí</label>
                                <input type="radio" 
                                    name="shipping_restriction_{$customer.customer_id}" 
                                    id="shipping_restriction_off_{$customer.customer_id}_0" 
                                    value="0" 
                                    {if $customer.shipping_restriction == 0}checked{/if} 
                                    class="shipping-restriction-toggle" 
                                    data-customer-id="{$customer.customer_id}">
                                <label for="shipping_restriction_off_{$customer.customer_id}_0" class="radioCheck">No</label>
                                <a class="slide-button btn"></a>
                            </span>
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
    {else}        
        <div class="alert alert-warning">
            {l s='Todavía no se ha registrado ningún cliente.'}
        </div>
    {/if}
</div>
<div id="url-shipping" data-value="{$link->getAdminLink('AdminClientManagement')}&shipping=1"></div>
<div id="token-container" data-token="{$_token}"></div>
<script type="text/javascript" src="{$module_dir}views/templates/js/admin_client_management.js"></script>