<% if $Items %>
    <div class="cart-summary">
        <h3>Summary</h3>
        <% loop $Items %>
            <div class="cartOverview__item $FirstLast">
                <p>$TableTitle</p>
                <% if $Image %>
                    {$Image.Fit(100,100)}
                <% end_if %>
                <div class="wrap">
                    <input class="quantity" value="$Quantity">
                    <div class="total">$Total.Nice</div>
                    <a href="$getSetQuantity" class="js-augment augment" data-id="$ID">Update</a>
                </div>
                <div class="clearfix"></div>
                <hr>
            </div>
        <% end_loop %>
        <hr>
        <div class="totals">
            <h5>Total <span>{$Total.Nice}$Currency</span></h5>
        </div>
    </div>
<% else %>
    <p class="message warning">
        <%t ShoppingCart.NoItems "There are no items in your cart." %>
    </p>
<% end_if %>
<% if $DisplayPromoButton %>
    <a href="{$Top.ShopApiUrl}/promocode/apply" class="button button--secondary">+ Add Promo Code</a>
<% end_if %>
