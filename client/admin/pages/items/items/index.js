/**
 * WordPress dependencies
 */
import { lazy } from '@wordpress/element';
/**
 * External dependencies
 */
import { Navigate, Routes, Route } from 'react-router-dom';

const List = lazy( () => import( './list' ) );
const View = lazy( () => import( './view' ) );
const Edit = lazy( () => import( './edit' ) );
const Create = lazy( () => import( './create' ) );

function Items( props ) {
	return (
		<div>
			<Routes>
				<Route path="/" element={ <List { ...props } /> } />
				<Route path="/create" element={ <Create { ...props } /> } />
				<Route path="/:id" element={ <View { ...props } /> } />
				<Route path="/:id/edit" element={ <Edit { ...props } /> } />
				<Route path="*" element={ <Navigate to="/" /> } />
			</Routes>
		</div>
	);
}

export default Items;
