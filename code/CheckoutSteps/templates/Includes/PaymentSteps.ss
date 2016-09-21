<% with $SiteConfig %>
    <% if $EnableCheckoutSteps %>
        <% if $CheckoutStepList %>
            <div class="">
                <div class="cartSteps">
                    <div class="cartStep cartSteps--color"><span>Select Item</span></div>
                    <% loop $CheckoutStepList %>
                        <div class="cartStep cartSteps--color cartSteps--{$LinkingMode}"><span>
                            <a href="{$Link}">{$MenuTitle}</a>
                        </span></div>
                    <% end_loop %>
                    <div class="cartStep cartSteps--complete"><span>Order Complete</span></div>
                </div>
            </div>
        <% end_if %>
    <% end_if %>
<% end_with %>
