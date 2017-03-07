<div class="innerWrap">

    <div class="typography">
        <h1>{$Title}</h1>

        <% if $PaymentErrorMessage %>
            <p class="message error">
                <%t CheckoutPage.PaymentErrorMessage 'Received error from payment gateway:' %>
                $PaymentErrorMessage
            </p>
        <% end_if %>

        $Content
    </div>

    <aside>
        <div id="Checkout">
            <% if $Cart %>
                <% with $Cart %>
                    <% include CartSummary %>
                    <% include PromoCode %>
                <% end_with %>
                $OrderForm
            <% else %>
                <p class="message warning"><%t ShoppingCart.NoItems "There are no items in your cart." %></p>
            <% end_if %>
        </div>
    </aside>

</div>
