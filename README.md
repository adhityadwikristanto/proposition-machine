# proposition-machine
version 2: dynamic variables and truth table


This app was build using laravel framework. This app is 5% propositional problem and 95% string-work-programming related problem. The main text/string processing files are in App/Http/Factory folder, you can jump there to quick look how this text processing. They are Table_of_truth.php and Text_processing.php. All of files in Factory folder are initiated by PropositionControlller.php which located in App/Http/Controllers folder.

Dynamic variables mean that we can put any letter as variable except F, T, V, and X because they will be used as part of operation. This app also can produce dynamic truth table combination based on variation of variables.

The operands used in this app, are:
'^' for AND,
'V' for OR,
'~' for negation,
'>' for imply,
'<>' for if only if,
'<+X+>' for XOR (without plus sign)


