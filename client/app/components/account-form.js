import { Button, Form, Input, message, Space, Drawer } from 'antd';

export default function AccountForm() {
	const [form] = Form.useForm();
	const onFinish = () => {
		message.success('Submit success!');
	};
	const onFinishFailed = () => {
		message.error('Submit failed!');
	};
	return (
		// eslint-disable-next-line no-undef
		<Drawer title="Basic Drawer" placement="right" open={true}>
			<Form
				form={form}
				layout="vertical"
				onFinish={onFinish}
				onFinishFailed={onFinishFailed}
				autoComplete="off"
			>
				<Form.Item
					name="url"
					label="URL"
					rules={[
						{
							required: true,
						},
						{
							type: 'url',
							warningOnly: true,
						},
						{
							type: 'string',
							min: 6,
						},
					]}
				>
					<Input placeholder="input placeholder" />
				</Form.Item>
				<Form.Item>
					<Space>
						<Button type="primary" htmlType="submit">
							Submit
						</Button>
					</Space>
				</Form.Item>
			</Form>
		</Drawer>
	);
}
