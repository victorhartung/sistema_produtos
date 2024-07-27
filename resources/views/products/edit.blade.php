@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-auto">
                <a href="{{ route('products.index') }}" class="btn btn-dark">
                    <div class="fas fa-arrow-left"></div> 
                    Voltar
                </a>
            </div>
            <div class="col">
                <h1 class="mb-0">Editar produto</h1>
            </div>
        </div>
        <hr>
        @if (count($errors) > 0)
            <div class="alert alert-danger shadow-sm">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <ul class="m-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('products.update', $product) }}" method="POST">
            @method('PUT')
            @csrf
            <div class="row">
                <div class="col-md-6 form-group">
                    <label for="name"><b>Nome</b></label>
                    <input type="text" id="name" name="name" class="form-control shadow-sm"
                        value="{{ old('name') ?? $product->name }}">
                </div>
                <div class="col-md-3 form-group">
                    <label for="cost_price"><b>
                            @if (!old('composite'))
                                Preço de custo
                            @else
                                Preço total de custo
                            @endif
                        </b></label>
                    <input type="text" id="cost_price" name="cost_price" class="form-control shadow-sm"
                        value="{{ old('cost_price') ?? $product->cost_price }}"
                        @if (old('composite') ?? $product->composite) readonly @endif>
                </div>
                <div class="col-md-3 form-group">
                    <label for="retail_price"><b>Preço de venda</b></label>
                    <input type="text" id="retail_price" name="retail_price" class="form-control shadow-sm"
                        value="{{ old('retail_price') ?? $product->retail_price }}">
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="custom-control custom-checkbox mb-3">
                        <input type="checkbox" class="custom-control-input" id="composite" name="composite"
                            @if (old('composite') ?? $product->composite) checked @endif>
                        <label class="custom-control-label" for="composite">Produto composto</label>
                    </div>
                </div>
            </div>
            <div id="composite_div" style="@if (!(old('composite') ?? $product->composite)) display: none @endif">
                <div class="row">
                    <div class="col-12 mb-3">
                        <input type="text" id="search_products" class="form-control">
                    </div>
                </div>
                <div class="row" id="composite">
                    <div class="col-12">
                        <table id="table_products" class="table table-striped table-hover shadow-sm w-100">
                            <thead class="bg-dark text-white">
                                <tr>
                                    <th>Produto</th>
                                    <th class="text-center">Preço custo</th>
                                    <th class="text-center">Quantidade</th>
                                    <th class="text-right">Subtotal</th>
                                    <th class="text-right"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach (old('info_products') ?? $product->compositeProducts as $k => $simple_product)
                                    @php
                                        if (old('info_products')) {
                                            $prod = $simple_product;
                                        } else {
                                            $prod = json_encode($simple_product->simple);
                                            $k = $simple_product->simple_id;
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="hidden" name="info_products[{{ $k }}]" value="{{ $prod }}">
                                            @php($prod = json_decode($prod))
                                            {{ $prod->name }}
                                        </td>
                                        <td class="text-right money">{{ $prod->cost_price }}</td>
                                        <td>
                                            <input type="number" class="form-control m-auto shadow-sm"
                                                name="products[{{ $k }}]"
                                                value="{{ old('products')[$k] ?? $simple_product->amount }}"
                                                min="1" style="max-width:10rem"
                                            > 
                                        </td>
                                        <td class="text-right money">
                                            {{ (float) str_replace(['.', ','], ['', '.'], $prod->cost_price) * (float) (old('products')[$k] ?? $simple_product->amount) }}
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger float-right shadow-sm remove">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> 
                        Salvar
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('js')
    <script>
        var products = [
            @foreach (old('info_products') ?? $product->compositeProducts as $k => $simple_product)
                @php($prod = old('info_products') ? $simple_product : json_encode($simple_product->simple))
                {!! $prod !!},
            @endforeach
        ];

        var total = 0;


        // Máscara para numero monetário
        $(".money").mask("000.000.000,00", { reverse: true }).trigger('input');

        $('.remove').click(function() {

            let index = $(this).closest('table').find('tbody tr').index($(this).closest('tr'))
            //console.log(produtos);
            products.splice(index, 1)
            //console.log(index);
            //console.log(produtos);
            $('#table_products tbody tr:eq(' + index + ')').remove()


        })

        function calculateTotalCost() {
            total = 0
            $("#table_products tbody tr").each(function(index) {
                let subtotal = $(this).find('td:eq(3)').html().replace('.', '').replace(',', '.')
                total += parseFloat(subtotal)
                $('#cost_price').val(parseFloat(total).toFixed(2)).trigger('input')
            });

        }

        $(document).ready(function() {
            $('#cost_price,#retail_price').mask("000.000.000,00", {
                reverse: true
            });
            $('#composite').change(function() {
                if ($(this).prop('checked')) {
                    $('#composite_div').show()
                    $('#cost_price').prop('readonly', true)
                        .parent().find('label b').html('Preço total de custo')
                } else {
                    $('#composite_div').hide()
                    $('#cost_price').prop('readonly', false)
                        .parent().find('label b').html('Preço de custo')
                }

            })
            search = $('#search_products').select2({
                language: "pt-BR",
                theme: 'bootstrap4',
                placeholder: "Adicionar produto simples",
                width: "100%",
                ajax: {
                    url: '{{ route('products.search') }}',
                    dataType: 'json',
                    data: function(params) {
                        var query = {
                            search: params.term,
                            not: products.map(i => i[`id`])
                        }
                        return query;
                    }
                }
            });

            $('#table_products input[type=number]').change(function() {
                // Alteracao do subtotal
                let index = $(this).closest('table').find(
                        'tbody tr')
                    .index($(this).closest('tr'))

                let subtotal = parseFloat(products[index]
                    .cost_price * $(this).val()).toFixed(2)

                $(this).closest('tr').find('td:eq(3)').html(
                    subtotal).mask("000.000.000,00", {
                    reverse: true
                }).trigger('input')

                calculateTotalCost();
            });

            search.on('select2:select', function() {
                id = $(this).val()
                $.get('{{ route('products.search') }}?id=' + id, function(data) {
                    products.push(data)

                    $("#table_products tbody").append(
                        $('<tr>', ).append([
                            $('<td>').append([
                                $('<input>', {
                                    type: 'hidden',
                                    name: 'info_products[' + data.id + ']',
                                    value: JSON.stringify(data)
                                }), data.name
                            ]),
                            $('<td>', {
                                class: 'text-right'
                            })
                            .html(data.cost_price)
                            .mask("000.000.000,00", {
                                reverse: true
                            }).trigger('input'),
                            $('<td>').html($('<input>', {
                                type: 'number',
                                name: 'products[' + data.id + ']',
                                class: 'form-control m-auto shadow-sm',
                                style: 'max-width:10rem',
                                value: 1,
                                min: 1
                            }).change(function() {
                                // Alteracao do subtotal
                                let index = $(this).closest('table').find(
                                        'tbody tr')
                                    .index($(this).closest('tr'))

                                let subtotal = parseFloat(products[index]
                                    .cost_price * $(this).val()).toFixed(2)

                                $(this).closest('tr').find('td:eq(3)').html(
                                    subtotal).mask("000.000.000,00", {
                                    reverse: true
                                }).trigger('input')

                                calculateTotalCost();
                            })),
                            $('<td>', {
                                class: 'text-right'
                            }).html(data.cost_price).mask("000.000.000,00", {
                                reverse: true
                            }).trigger('input'),
                            $('<td>').append($('<button>', {
                                type: 'button',
                                class: 'btn btn-danger float-right shadow-sm'
                            }).html($('<i>', {
                                class: 'fas fa-times'
                            })).click(function() {

                                let index = $(this).closest('table').find(
                                        'tbody tr')
                                    .index($(this).closest('tr'))
                                products.splice(index, 1)

                                $('#table_products tbody tr:eq(' + index + ')')
                                    .remove()

                            }));

                        ]));
                    calculateTotalCost();

                });
            });

        });
    </script>
@endpush