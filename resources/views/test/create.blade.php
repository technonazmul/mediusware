<form action="http://127.0.0.1:8000/product" method="post">
@csrf
<input type="text" name="title">
<input type="text" name="sku">
<input type="text" name="description">
<input type="submit" value="save">
</form>