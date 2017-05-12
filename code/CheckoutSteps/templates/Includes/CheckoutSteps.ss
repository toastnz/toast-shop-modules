<ul class="steps">
    <% loop $Steps %>
        <li data-api-url="{$Link}">{$Pos} {$Title} <% if $IsCurrent %>(active)<% end_if %></li>
    <% end_loop %>
</ul>