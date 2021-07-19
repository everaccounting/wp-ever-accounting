Link
===

Use `Link` to create a link to another resource. It accepts a type to automatically
create eaccounting links, eaccounting links, and external links.

## Usage

```jsx
<Link
	href="edit.php?post_type=shop_coupon"
	type="eaccounting"
>
	Coupons
</Link>
```

### Props

Name | Type | Default | Description
--- | --- | --- | ---
`href` | String | `null` | (required) The resource to link to
`type` | One of: 'eaccounting', 'eaccounting', 'external' | `'eaccounting'` | Type of link. For eaccounting and eaccounting, the correct prefix is appended
