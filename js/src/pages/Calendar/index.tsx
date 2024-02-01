import React, { useState } from 'react'
import dayjs, { Dayjs } from 'dayjs'
import { Calendar, Modal, Form, DatePicker, Button, Spin, Select, Input, Badge } from 'antd'
// eslint-disable-next-line no-duplicate-imports
import type { DatePickerProps, CalendarProps, BadgeProps } from 'antd'
import { useUpdate, useMany } from '@/hooks'
import { kebab, snake, currentUserId } from '@/utils/env'
import { useQueryClient } from '@tanstack/react-query'
import _ from 'lodash-es'
// import { SearchInput } from './SearchInput'

const App: React.FC = () => {
    const queryClient = useQueryClient()
    const [createModalOpen, setCreateModalOpen] = useState(false)
    const [viewModalOpen, setViewModalOpen] = useState(false)
    const [isDisabled, setIsDisabled] = useState(false)
    const [selectedPostId, setSelectedPostId] = useState<number | null>(null)
    const [value, setValue] = useState(() => dayjs())
    const [_selectedValue, setSelectedValue] = useState(() => dayjs())
    const [createForm] = Form.useForm()
    const [viewForm] = Form.useForm()
    //取得日記useMany
    const { data: currentPost, isFetching: currentPostLoading } = useMany({
        resource: kebab,
        args: {
            status: 'any',
            author: currentUserId,
        },
    })
    const { data: allPost, isFetching: allPostLoading } = useMany({
        resource: kebab,
    })
    const loading = currentPostLoading || allPostLoading
    const allPostData = allPost?.data || []
    const currentPostData = currentPost?.data || []
    const postArr = _.uniqBy([...allPostData, ...currentPostData], 'id')

    //新增日記useUpdate
    const { mutate, isLoading: createLoading } = useUpdate({
        resource: kebab,
        mutationOptions: {
            onSuccess: () => {
                setCreateModalOpen(false)
                createForm.resetFields()
                queryClient.invalidateQueries([`get_${kebab}s`])
            },
        },
    })
    //更新日記useUpdate
    const { mutate: update, isLoading: updateLoading } = useUpdate({
        resource: kebab,
        pathParams: [selectedPostId?.toString() || ''],
        mutationOptions: {
            onSuccess: () => {
                // setCreateModalOpen(false)
            },
        },
    })
    //日曆Calendar handle
    const onSelect = (newValue: Dayjs, info: { source: 'year' | 'month' | 'date' | 'customize' }) => {
        if (info.source === 'date') {
            setValue(newValue)
            setSelectedValue(newValue)
            setCreateModalOpen(true)
            createForm.setFieldsValue({ date: newValue })
        }
    }

    const onPanelChange = (newValue: Dayjs) => {
        console.log('onPanelChange', newValue)
        setValue(newValue)
    }

    //彈窗Modal handle
    const handleCancel = () => {
        setCreateModalOpen(false)
        setViewModalOpen(false)
    }

    //日期選擇器DataPicker onChange
    const handleDatePickerChange: DatePickerProps['onChange'] = (newValue, _dateString) => {
        setSelectedValue(newValue as Dayjs)
    }

    //表單Form onFinish
    const onFinish = (values: any) => {
        // console.log('Success:', values)
        mutate({
            title: values.title,
            date: values.date,
            content: values.content,
            status: values.status,
            meta: {
                [`${snake}_meta`]: values.friend,
            },
        })
    }
    const onUpdate = (values: any) => {
        // console.log('Success:', values)
        update({
            id: values.id,
            title: values.title,
            date: values.date,
            content: values.content,
            status: values.status,
            meta: {
                [`${snake}_meta`]: values.friend,
            },
        })
    }

    //切換為日曆時的render
    const dateCellRender = (renderValue: Dayjs) => {
        const listData = postArr.filter((item) => dayjs(item.date).date() === renderValue.date() && dayjs(item.date).month() === renderValue.month() && dayjs(item.date).year() === renderValue.year()) || []
        if (renderValue)
            return (
                <ul className="events">
                    {listData.map((item) => {
                        const status = item.status === 'publish' ? 'success' : 'error'
                        //TODO待做頭項功能
                        return (
                            <li
                                className="eventsItem"
                                key={item.id}
                                onClick={(event) => {
                                    // 阻止事件冒泡
                                    event.stopPropagation()
                                    setSelectedPostId(item.id)
                                    setViewModalOpen(true)
                                    // 判斷是否為自己的日記
                                    if (item.author.toString() === currentUserId) {
                                        setIsDisabled(false)
                                    } else {
                                        setIsDisabled(true)
                                    }
                                    //DOM解析器
                                    const parser = new DOMParser()
                                    const doc = parser.parseFromString(item.content.rendered, 'text/html')
                                    viewForm.setFieldsValue({
                                        id: item.id,
                                        date: dayjs(item.date),
                                        title: item.title.rendered.replace(/^私密內容:\s*/, ''),
                                        content: doc.body.textContent,
                                        status: item.status,
                                    })
                                }}
                            >
                                <Badge status={status as BadgeProps['status']} text={item.title.rendered.replace(/^私密內容:\s*/, '')} />
                            </li>
                        )
                    })}
                </ul>
            )
    }

    //日記cellRender 切換為日曆及月曆時的render switch
    const cellRender: CalendarProps<Dayjs>['cellRender'] = (current, info) => {
        if (info.type === 'date') return dateCellRender(current)
        // if (info.type === 'month') return monthCellRender(current)
        return info.originNode
    }
    return (
        <div>
            <Spin spinning={loading}>
                <Calendar value={value} onSelect={onSelect} onPanelChange={onPanelChange} cellRender={cellRender} />
            </Spin>
            <Modal title="新增日記" open={createModalOpen} onCancel={handleCancel} footer={null}>
                <Spin spinning={createLoading}>
                    <Form form={createForm} onFinish={onFinish}>
                        <Form.Item label="時間" name="date">
                            <DatePicker onChange={handleDatePickerChange} />
                        </Form.Item>
                        <Form.Item label="標題" name="title">
                            <Input type="text" />
                        </Form.Item>
                        <Form.Item label="內容" name="content">
                            <Input.TextArea />
                        </Form.Item>
                        <Form.Item label="狀態" name="status" initialValue={'private'}>
                            <Select>
                                <Select.Option value="private">私人</Select.Option>
                                <Select.Option value="publish">公開</Select.Option>
                            </Select>
                        </Form.Item>
                        {/* 分享給朋友功能 等list做完後做 */}
                        {/* <Form.Item label="分享給朋友" name="friend">
                            <SearchInput placeholder="input search text" />
                        </Form.Item> */}
                        <Form.Item>
                            <Button className="h-full text-[#1e73be] font-bold  border-[#1e73be] hover:bg-[#1e73be] hover:text-white hover:border-white focus:text-white focus:bg-[#1e73be] focus:outline-0" type="primary" htmlType="submit">
                                新增日記
                            </Button>
                        </Form.Item>
                    </Form>
                </Spin>
            </Modal>
            <Modal title="查看日記" open={viewModalOpen} onCancel={handleCancel} footer={null}>
                <Spin spinning={updateLoading}>
                    <Form form={viewForm} onFinish={onUpdate} disabled={isDisabled}>
                        <Form.Item label="id" name="id" hidden>
                            <Input type="text" />
                        </Form.Item>
                        <Form.Item label="時間" name="date">
                            <DatePicker onChange={handleDatePickerChange} />
                        </Form.Item>
                        <Form.Item label="標題" name="title">
                            <Input type="text" />
                        </Form.Item>
                        <Form.Item label="內容" name="content">
                            <Input.TextArea />
                        </Form.Item>
                        <Form.Item label="狀態" name="status" initialValue={'private'}>
                            <Select>
                                <Select.Option value="private">私人</Select.Option>
                                <Select.Option value="publish">公開</Select.Option>
                            </Select>
                        </Form.Item>
                        {/*TODO 分享給朋友功能 等list做完後做 */}
                        {/* <Form.Item label="分享給朋友" name="friend">
                            <SearchInput placeholder="input search text" />
                        </Form.Item> */}
                        {isDisabled && <p>您無權編輯此日記</p>}
                        {!isDisabled && (
                            <Form.Item>
                                <Button className="h-full text-[#1e73be] font-bold  border-[#1e73be] hover:bg-[#1e73be] hover:text-white hover:border-white focus:text-white focus:bg-[#1e73be] focus:outline-0" type="primary" htmlType="submit">
                                    更新
                                </Button>
                            </Form.Item>
                        )}
                    </Form>
                </Spin>
            </Modal>
        </div>
    )
}

export default App
