<?php

namespace EverAccounting\Models\Traits;

/**
 * HasAttributes trait.
 *
 * @since 1.0.0
 * @package ByteKit\Models
 */
class HasAttributes {
	/**
	 * The model's attributes.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * The model attribute's original state.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $original = array();

	/**
	 * The attributes that should be cast to native types.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $casts = array();

	/**
	 * Attribute aliases.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $aliases = array();

	/**
	 * The accessors to append to the model's array form.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $appends = array();

	/**
	 * The attributes that should be hidden for array.
	 *
	 * @since 1.0.0
	 * @var array
	 */
	protected $hidden = array();

	/**
	 * Get the attributes.
	 *
	 * @return array
	 */
	public function get_attributes() {
		return $this->attributes;
	}

	/**
	 * Determine whether the model has an attribute.
	 *
	 * @param string $key The attribute key.
	 *
	 * @return bool
	 */
	public function has_attribute( $key ) {
		return array_key_exists( $key, $this->attributes ) || $this->has_alias( $key );
	}

	/**
	 * Get an attribute from the model.
	 *
	 * @param string $key The attribute key.
	 *
	 * @return mixed|void The attribute value.
	 */
	public function get_attribute( $key ) {
		if ( array_key_exists( $key, $this->attributes ) ||
		     array_key_exists( $key, $this->casts ) ||
		     $this->has_alias( $key ) ||
		     $this->has_get_mutator( $key ) ) {
			return $this->get_attribute_value( $key );
		}

		// Here we will determine if the model base class itself contains this given key
		// since we don't want to treat any of those methods as relationships because
		// they are all intended as helper methods and none of these are relations.
		if ( method_exists( self::class, $key ) ) {
			return;
		}

		// If the key already exists in the relationships array, it just means the
		if ( $this->has_relation( $key ) ) {
			return $this->get_relationship( $key );
		}

		// Finally, we will assume the attribute is a meta field.
		if ( $this->has_metadata_support() && $this->is_valid_meta_key( $key ) && $this->has_meta( $key ) ) {
			return $this->cast_attribute( $key, $this->get_meta( $key ) );
		}

		return;
	}

	/**
	 * Get the original attributes.
	 *
	 * @return array
	 */
	public function get_original() {
		return $this->original;
	}

	/**
	 * Set the original attributes.
	 *
	 * @param array $original The original attributes to merge.
	 *
	 * @since 1.0.0
	 * @return $this
	 */
	public function set_original( array $original ) {
		$this->original = array_merge( $this->original, $original );

		return $this;
	}

	/**
	 * Check if the model or any of the given attributes have been modified.
	 *
	 * @return bool
	 */
	public function has_changes() {
		return count( $this->get_changes() ) > 0;
	}

	/**
	 * Get the attributes that have been changed since the last sync.
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function get_changes() {
		$changed = array();
		foreach ( array_keys( $this->attributes ) as $key ) {
			if ( $this->is_attribute_changed( $key ) ) {
				$changed[ $key ] = $this->get_attribute( $key );
			}
		}

		return $changed;
	}

	/**
	 * Determine if the model or any of the given attributes have remained the same.
	 *
	 * @param string $key The attribute key.
	 *
	 * @return bool
	 */
	public function is_attribute_changed( $key ) {
		if ( ! array_key_exists( $key, $this->get_original() ) ) {
			return true;
		}
		$attribute = array_key_exists( $key, $this->attributes ) ? $this->attributes[ $key ] : null;
		$original  = array_key_exists( $key, $this->original ) ? $this->original[ $key ] : null;
		if ( $attribute === $original ) {
			return false;
		} elseif ( is_null( $attribute ) ) {
			return true;
		} elseif ( $this->is_date_attribute( $key ) ) {
			return $this->cast_date( $attribute ) !== $this->cast_date( $original );
		} elseif ( $this->has_cast( $key, array( 'real', 'float', 'double' ) ) ) {
			if ( ( null === $attribute && null !== $original ) || ( null !== $attribute && null === $original ) ) {
				return true;
			}

			return abs( $this->cast_attribute( $key, $attribute ) - $this->cast_attribute( $key, $original ) ) > PHP_FLOAT_EPSILON * 4;
		} elseif ( $this->has_cast( $key ) ) {
			return $this->cast_attribute( $key, $attribute ) !== $this->cast_attribute( $key, $original );
		} elseif ( is_numeric( $attribute ) && is_numeric( $original ) && strcmp( (string) $attribute, (string) $original ) !== 0 ) {
			return true;
		}

		return $attribute !== $original;
	}

	/**
	 * Determine whether an attribute should be cast to a native type.
	 *
	 * @param string            $key The attribute key.
	 * @param array|string|null $types The cast types.
	 *
	 * @return bool
	 */
	public function has_cast( $key, $types = null ) {
		if ( array_key_exists( $key, $this->get_casts() ) ) {
			return ! $types || in_array( $this->get_cast_type( $key ), (array) $types, true );
		}

		return false;
	}

	/**
	 * Get the casts array.
	 *
	 * @return array
	 */
	public function get_casts() {
		return $this->casts;
	}

	/**
	 * Set the casts array.
	 *
	 * @param array $casts The casts to merge.
	 *
	 * @return $this
	 */
	public function set_casts( array $casts ) {
		$this->casts = array_unique(
			array_merge( $this->casts, is_string( $casts ) ? func_get_args() : $casts )
		);

		return $this;
	}

	/**
	 * Get the type of cast for a model attribute.
	 *
	 * @param string $key The attribute key.
	 *
	 * @return string
	 */
	protected function get_cast_type( $key ) {
		return trim( strtolower( $this->casts[ $key ] ) );
	}

	/**
	 * Cast an attribute to a native PHP type.
	 *
	 * @param string $key The attribute key.
	 * @param mixed  $value The attribute value.
	 *
	 * @return mixed
	 */
	protected function cast_attribute( $key, $value ) {
		if ( is_null( $value ) ) {
			return $value;
		}

		switch ( $this->get_cast_type( $key ) ) {
			case 'int':
			case 'integer':
				$value = (int) $value;
				break;
			case 'real':
			case 'float':
			case 'double':
				$value = (float) $value;
				break;
			case 'bool':
			case 'boolean':
				$value = (bool) filter_var( $value, FILTER_VALIDATE_BOOLEAN );
				break;
			case 'object':
				$value = json_decode( $value );
				break;
			case 'array':
			case 'json':
				$value = json_decode( $value, true );
				break;
			case 'date':
				$value = ! empty( $this->cast_datetime( $value ) ) ? wp_date( 'Y-m-d', strtotime( $value ) ) : null;
				break;
			case 'time':
				$value = ! empty( $this->cast_datetime( $value ) ) ? wp_date( 'H:i:s', strtotime( $value ) ) : null;
				break;
			case 'datetime':
				$value = ! empty( $this->cast_datetime( $value ) ) ? wp_date( 'Y-m-d H:i:s', strtotime( $value ) ) : null;
				break;
			case 'timestamp':
				$value = ! empty( $this->cast_datetime( $value ) ) ? wp_date( 'U', strtotime( $value ) ) : null;
				break;
			case 'string':
			default:
				if ( is_numeric( $value ) ) {
					if ( str_contains( $value, '.' ) ) {
						$value = (float) $value;
					} else {
						$value = (int) $value;
					}
				}
				break;
		}

		return $value;
	}

	/**
	 * Cast the given attribute to a date.
	 *
	 * @param mixed $value The value to cast.
	 *
	 * @return string|null The casted date.
	 */
	protected function cast_datetime( $value ) {
		$datetime = date_parse( $value );
		if ( empty( $datetime['error_count'] ) && empty( $datetime['warning_count'] ) ) {
			// Check if the date is a valid Gregorian calendar date.
			if ( checkdate( $datetime['month'], $datetime['day'], $datetime['year'] ) ) {
				// If time is provided, validate it as well.
				if ( isset( $datetime['hour'], $datetime['minute'], $datetime['second'] ) ) {
					return $datetime['hour'] >= 0 && $datetime['hour'] < 24 &&
					       $datetime['minute'] >= 0 && $datetime['minute'] < 60 &&
					       $datetime['second'] >= 0 && $datetime['second'] < 60;
				}

				return strtotime( $value );
			}
		}

		return null;
	}

	/**
	 * Determine if the given attribute is a date or date castable.
	 *
	 * @param string $key The attribute key.
	 *
	 * @return bool
	 */
	protected function is_date_attribute( $key ) {
		return $this->has_cast( $key, array( 'date', 'time', 'datetime', 'timestamp' ) );
	}

	/**
	 * Determine whether an attribute have an alias.
	 *
	 * @param string $key The attribute key.
	 *
	 * @return bool
	 */
	public function has_alias( $key ) {
		return array_key_exists( $key, $this->get_aliases() );
	}

	/**
	 * Get the aliases array.
	 *
	 * @return array
	 */
	public function get_aliases() {
		return $this->aliases;
	}

	/**
	 * Set the aliases array.
	 *
	 * @param array|string $aliases The aliases to merge.
	 *
	 * @return $this
	 */
	public function set_aliases( $aliases ) {
		$this->aliases = array_unique(
			array_merge( $this->aliases, is_string( $aliases ) ? func_get_args() : $aliases )
		);

		return $this;
	}

	/**
	 * Get the attribute key for the alias.
	 *
	 * @param string $alias The alias.
	 *
	 * @return string
	 */
	protected function get_alias_attribute( $alias ) {
		if ( array_key_exists( $alias, $this->aliases ) ) {
			return $this->aliases[ $alias ];
		}

		return $alias;
	}

	/**
	 * Get the alias for the attribute key.
	 *
	 * @param string $key The attribute key.
	 *
	 * @return string
	 */
	protected function get_attribute_alias( $key ) {
		if ( in_array( $key, $this->aliases, true ) ) {
			return array_search( $key, $this->aliases, true );
		}

		return $key;
	}

	/**
	 * Return whether the accessor attribute has been appended.
	 *
	 * @param string $attribute The attribute key.
	 *
	 * @return bool
	 */
	public function has_appended( $attribute ) {
		return in_array( $attribute, $this->appends, true );
	}

	/**
	 * Get the appended attributes.
	 *
	 * @return array
	 */
	public function get_appends() {
		return $this->appends;
	}

	/**
	 * Set the appended attributes.
	 *
	 * @param array|string $appends The appended attributes to merge.
	 *
	 * @return $this
	 */
	public function set_appends( $appends ) {
		$this->appends = array_unique(
			array_merge( $this->appends, is_string( $appends ) ? func_get_args() : $appends )
		);

		return $this;
	}

	/**
	 * Determine whether an attribute should be hidden.
	 *
	 * @param string $key The attribute key.
	 *
	 * @return bool
	 */
	public function is_hidden( $key ) {
		return in_array( $key, $this->get_hidden(), true );
	}

	/**
	 * Get the hidden attributes.
	 *
	 * @return array
	 */
	public function get_hidden() {
		return $this->hidden;
	}

	/**
	 * Set the hidden attributes.
	 *
	 * @param array|string $hidden The hidden attributes to merge.
	 *
	 * @return $this
	 */
	public function set_hidden( $hidden ) {
		$this->hidden = array_unique(
			array_merge( $this->hidden, is_string( $hidden ) ? func_get_args() : $hidden )
		);

		return $this;
	}

	/**
	 * Determine if a get mutator exists for an attribute.
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function has_get_mutator( $key ) {
		return method_exists( $this, 'get_' . $key . '_attribute' );
	}

	/**
	 * Get the value of an attribute using its mutator.
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return mixed
	 */
	protected function mutate_attribute( $key, $value ) {
		return $this->{'get_' . $key . '_attribute'}( $value );
	}

	/**
	 * Determine if a set mutator exists for an attribute.
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function has_set_mutator( $key ) {
		return method_exists( $this, 'set_' . $key . '_attribute' );
	}

	/**
	 * Set the value of an attribute using its mutator.
	 *
	 * @param string $key The attribute key.
	 * @param mixed  $value The attribute value.
	 *
	 * @return mixed
	 */
	protected function set_mutated_attribute_value( $key, $value ) {
		return $this->{'set_' . $key . '_attribute'}( $value );
	}

	/**
	 * Determine if an "Attribute" return type marked mutator exists for an attribute.
	 *
	 * @param string $key
	 *
	 * @return bool
	 */
	public function has_mutator( $key ) {
		return $this->has_get_mutator( $key ) || $this->has_set_mutator( $key );
	}

	/**
	 * Determine if the given key is a relationship method on the model.
	 *
	 * @param string $key The key.
	 *
	 * @return bool
	 */
	public function has_relation( $key ) {
		return false;
	}
}
