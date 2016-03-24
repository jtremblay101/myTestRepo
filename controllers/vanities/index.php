<?
include( $_SERVER['DOCUMENT_ROOT']."/assets\DataTables\Editor-PHP-1.5.5\php/DataTables.php" );
 
// Alias Editor classes so they are easy to use
use
    DataTables\Editor,
    DataTables\Editor\Field,
    DataTables\Editor\Format,
    DataTables\Editor\Mjoin,
    DataTables\Editor\Upload,
    DataTables\Editor\Validate;
 
// Build our Editor instance and process the data coming from _POST
Editor::inst( $db, 'datatables_demo' )
    ->fields(
        Field::inst( 'RowKey' )->validator( 'Validate::notEmpty' ),
        Field::inst( 'Destination' )->validator( 'Validate::notEmpty' )
    )
    ->process( $_POST )
    ->json();
	