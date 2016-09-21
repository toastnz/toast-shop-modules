<% if $IncludeFormTag %>
    <form $AttributesHTML>
<% end_if %>
<% if $Message %>
        <div id="{$FormName}_error" class="message $MessageType">$Message</div>
<% else %>
        <div id="{$FormName}_error" class="message $MessageType" style="display: none"></div>

        <fieldset>
            <% if $Legend %><legend>$Legend</legend><% end_if %>
            <% loop $Fields %>
                $FieldHolder
            <% end_loop %>
            <div class="clear"><!-- --></div>
        </fieldset>

    <% if $Actions %>
            <div class="Actions">
                <% loop $Actions %>
                    $Field
                <% end_loop %>
            </div>
    <% end_if %>
<% end_if %>

<% if $IncludeFormTag %>
    </form>
<% end_if %>