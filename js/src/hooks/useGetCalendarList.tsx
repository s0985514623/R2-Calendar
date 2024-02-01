import { useMany } from '@/hooks'
import { kebab, currentUserId } from '@/utils/env'
export const useGetCalendarList = () => {
    //取得日記useMany
    const { data: currentPost, isLoading: currentPostLoading } = useMany({
        resource: kebab,
        args: {
            status: 'any',
            author: currentUserId,
        },
    })
    const { data: allPost, isLoading: allPostLoading } = useMany({
        resource: kebab,
    })
    const loading = currentPostLoading || allPostLoading
    const postArr = [...allPost?.data, ...currentPost?.data] || []
    console.log('🚀 ~ postArr:', postArr)

    return postArr
}
