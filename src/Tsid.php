<?php

namespace Odan\Tsid;

class Tsid
{
    public function __construct(private $number = 0) {
	}

	public function __toString() {
		return static::encode($this->number);
	}

	public function toInt() {
		return $this->number;
	}

	public function toString(): string {
		return static::encode($this->number);
	}

	public static function fromInt($number): TSID {
		return new Tsid($number);
	}

	public static function fromString($string): TSID {
		return new Tsid(static::decode($string));
	}

    public function equals(Tsid $other): bool
    {
        return $this->number === $other->number;
    }
    
    public static $symbols = array(
		'0',
		'1',
		'2',
		'3',
		'4',
		'5',
		'6',
		'7',
		'8',
		'9',
		'A',
		'B',
		'C',
		'D',
		'E',
		'F',
		'G',
		'H',
		'J',
		'K',
		'M',
		'N',
		'P',
		'Q',
		'R',
		'S',
		'T',
		'V',
		'W',
		'X',
		'Y',
		'Z',
		'*',
		'~',
		'$',
		'=',
		'U',
	);

	public static $flippedSymbols = array(
		'0' => 0,
		'1' => 1,
		'2' => 2,
		'3' => 3,
		'4' => 4,
		'5' => 5,
		'6' => 6,
		'7' => 7,
		'8' => 8,
		'9' => 9,
		'A' => 10,
		'B' => 11,
		'C' => 12,
		'D' => 13,
		'E' => 14,
		'F' => 15,
		'G' => 16,
		'H' => 17,
		'J' => 18,
		'K' => 19,
		'M' => 20,
		'N' => 21,
		'P' => 22,
		'Q' => 23,
		'R' => 24,
		'S' => 25,
		'T' => 26,
		'V' => 27,
		'W' => 28,
		'X' => 29,
		'Y' => 30,
		'Z' => 31,
		'*' => 32,
		'~' => 33,
		'$' => 34,
		'=' => 35,
		'U' => 36,
	);

	public static function encode( $number ) {
		if ( ! is_numeric( $number ) ) {
			throw new \RuntimeException( "Specified number '{$number}' is not numeric" );
		}

		if ( ! $number ) {
			return "0";
		}

		$response = array();
		while ( $number ) {
			$remainder  = $number % 32;
			$number     = (int) ( $number / 32 );
			$response[] = static::$symbols[ $remainder ];
		}

		return implode( '', array_reverse( $response ) );
	}
    
    public static function normalize( $string, $errmode = self::NORMALIZE_ERRMODE_SILENT ) {
		$origString = $string;

		$string = strtoupper( $string );
		if ( $string !== $origString && $errmode ) {
			throw new \RuntimeException( "String '$origString' requires normalization" );
		}

		$string = str_replace( '-', '', strtr( $string, 'IiLlOo', '111100' ) );
		if ( $string !== $origString && $errmode ) {
			throw new \RuntimeException( "String '$origString' requires normalization" );
		}

		return $string;
	}

	protected static function decode( $string, $errmode = self::NORMALIZE_ERRMODE_SILENT, $isChecksum = false ) {
		if ( '' === $string ) {
			return '';
		}

		if ( null === $string ) {
			return '';
		}

		$string = static::normalize( $string, $errmode );

		if ( $isChecksum ) {
			$valid = '/^[A-Z0-9\*\~\$=U]$/';
		} else {
			$valid = '/^[A-TV-Z0-9]+$/';
		}

		if ( ! preg_match( $valid, $string ) ) {
			throw new \RuntimeException( "String '$string' contains invalid characters" );
		}

		$total = 0;
		foreach ( str_split( $string ) as $symbol ) {
			$total = $total * 32 + static::$flippedSymbols[ $symbol ];
		}

		return $total;
	}
}
