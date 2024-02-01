import React, { useState } from 'react'
import { Select } from 'antd'
// eslint-disable-next-line no-duplicate-imports
import type { SelectProps } from 'antd'
// import { useAjax } from '@/hooks/useAjax'

//TODO 分享給朋友功能 等list做完後做
export const SearchInput: React.FC<{ placeholder: string; style?: React.CSSProperties; className?: string }> = (props) => {
    const [data, setData] = useState<SelectProps['options']>([])
    const [value, setValue] = useState<string>()
    // const { mutate, isLoading } = useAjax({
    //     mutationOptions: {
    //         onSuccess: () => {},
    //         onError: (error) => {},
    //     },
    // })

    //後做 發AJAX請求取得user資料
    const handleSearch = (newValue: string) => {
        // mutate({
        //     action: 'handle_update_post_meta',
        //     post_id: postId as number,
        //     meta_key: `${snake}_meta`,
        //     meta_value: JSON.stringify(allFields),
        // })
        if (newValue) {
            setData(
                Array.from({ length: 10 }, (_, i) => ({
                    value: i.toString(),
                    label: newValue,
                })),
            )
        } else {
            setData([])
        }
    }

    const handleChange = (newValue: string) => {
        setValue(newValue)
    }

    return (
        <Select
            showSearch
            mode="multiple"
            value={value}
            placeholder={props.placeholder}
            style={props.style}
            className={props.className}
            defaultActiveFirstOption={false}
            onSearch={handleSearch}
            onChange={handleChange}
            loading
            options={(data || []).map((d) => ({
                value: d.value,
                label: d.text,
            }))}
        />
    )
}
