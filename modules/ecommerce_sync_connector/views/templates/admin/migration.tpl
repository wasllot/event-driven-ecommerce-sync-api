<div class="panel">
    <div class="panel-heading">
        <i class="icon-cloud-download"></i> {l s='Product Migration from Source' mod='ecommerce_sync_connector'}
    </div>
    
    <div class="alert alert-info">
        {l s='Select products to migrate from the Wholesaler.' mod='ecommerce_sync_connector'}
    </div>

    <div id="migration-app">
        <button id="btn-load-products" class="btn btn-primary" onclick="loadProducts()">
            <i class="icon-refresh"></i> {l s='Load Available Products' mod='ecommerce_sync_connector'}
        </button>

        <button id="btn-migrate-selected" class="btn btn-success" onclick="migrateSelected()" style="display:none; margin-left: 10px;">
            <i class="icon-download"></i> {l s='Migrate Selected' mod='ecommerce_sync_connector'}
        </button>

        <div id="loader" style="display:none; margin-top: 15px;">
            <i class="icon-spinner icon-spin icon-large"></i> {l s='Loading...' mod='ecommerce_sync_connector'}
        </div>

        <table class="table" id="products-table" style="margin-top: 20px; display:none;">
            <thead>
                <tr>
                    <th><input type="checkbox" id="select-all" onclick="toggleSelectAll()"></th>
                    <th>ID</th>
                    <th>Reference</th>
                    <th>Name</th>
                    <th>Stock</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <!-- Rows will be injected here -->
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    const apiUrl = "{$api_url}";
    const apiToken = "{$api_token}";

    function loadProducts() {
        $('#loader').show();
        $('#products-table').hide();
        $('#products-table tbody').empty();

        $.ajax({
            url: apiUrl + '/products',
            type: 'GET',
            headers: { 'X-API-KEY': apiToken },
            success: function(response) {
                $('#loader').hide();
                if (response.length > 0) {
                    $('#products-table').show();
                    $('#btn-migrate-selected').show();
                    
                    response.forEach(function(product) {
                        $('#products-table tbody').append(
                            `<tr>
                                <td><input type="checkbox" class="product-select" value="${product.id}"></td>
                                <td>${product.id}</td>
                                <td>${product.reference}</td>
                                <td>${product.name}</td>
                                <td>${product.stock}</td>
                                <td>${product.price}</td>
                            </tr>`
                        );
                    });
                } else {
                    alert('No products found.');
                }
            },
            error: function() {
                $('#loader').hide();
                alert('Error fetching products from API.');
            }
        });
    }

    function toggleSelectAll() {
        $('.product-select').prop('checked', $('#select-all').prop('checked'));
    }

    function migrateSelected() {
        const selectedIds = $('.product-select:checked').map(function() {
            return $(this).val();
        }).get();

        if (selectedIds.length === 0) {
            alert('Please select at least one product.');
            return;
        }

        if (!confirm('Are you sure you want to migrate ' + selectedIds.length + ' products?')) {
            return;
        }

        $('#loader').show();

        $.ajax({
            url: apiUrl + '/products/migrate',
            type: 'POST',
            headers: { 
                'X-API-KEY': apiToken,
                'Content-Type': 'application/json' 
            },
            data: JSON.stringify({ ids: selectedIds.map(Number) }),
            success: function(response) {
                $('#loader').hide();
                alert(response.message);
            },
            error: function() {
                $('#loader').hide();
                alert('Error queuing migration.');
            }
        });
    }
</script>
