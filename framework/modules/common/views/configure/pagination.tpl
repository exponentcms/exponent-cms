<h2>{"Pagination"|gettext}</h2>
{control type=text name=limit label="Items per page"|gettext value=$config.limit}
{control type=dropdown name=pagelinks label="Show page links" items="Top and Bottom,Top Only,Bottom Only,Don't show page links" checked=$config.pagelinks}
