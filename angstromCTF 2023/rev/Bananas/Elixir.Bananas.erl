-file("lib/bananas.ex", 1).

-module('Elixir.Bananas').

-compile([no_auto_import]).

-export(['__info__'/1,main/0,main/1]).

-spec '__info__'(attributes | compile | functions | macros | md5 |
                 exports_md5 | module | deprecated) ->
                    any().

'__info__'(module) ->
    'Elixir.Bananas';
'__info__'(functions) ->
    [{main, 0}, {main, 1}];
'__info__'(macros) ->
    [];
'__info__'(exports_md5) ->
    <<"Tю}оз|╨6Ч\020м\f\035\005\222\203">>;
'__info__'(Key = attributes) ->
    erlang:get_module_info('Elixir.Bananas', Key);
'__info__'(Key = compile) ->
    erlang:get_module_info('Elixir.Bananas', Key);
'__info__'(Key = md5) ->
    erlang:get_module_info('Elixir.Bananas', Key);
'__info__'(deprecated) ->
    [].

check([_num@1, <<"bananas">>]) ->
    (_num@1 + 5) * 9 - 1 == 971;
check(__asdf@1) ->
    false.

convert_input(_string@1) ->
    to_integer('Elixir.String':split('Elixir.String':trim(_string@1))).

main() ->
    main([]).

main(_args@1) ->
    print_flag(check(convert_input('Elixir.IO':gets(<<"How many bananas"
                                                      " do I have?\n">>)))).

print_flag(false) ->
    'Elixir.IO':puts(<<"Nope">>);
print_flag(true) ->
    'Elixir.IO':puts('Elixir.File':'read!'(<<"flag.txt">>)).

to_integer([_num@1, _string@1]) ->
    [binary_to_integer(_num@1), _string@1];
to_integer(_list@1) ->
    _list@1.

