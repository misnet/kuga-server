# 注意：
1、auth.json 中 配置了访问gitlab.com的相关token，如果需要配置gitlab仓库的访问，可以参考下面示例：

2、composer.json中的引用只是示例，除了kuga-openapi-core、qing、incubator 是必须外，其他请根据需要进行引用。
```
{
  "secure-http": false,
  "github-oauth": {
    "github.com": "这里填token",
    "hub.fastgit.org":"hub.fastgit.org是github的中国镜像，这里填token"
  },
  "gitlab-oauth": {
    "gitlab.depoga.cn": "这里填token"
  },
  "gitlab-token": {
    "gitlab.depoga.cn": "这里填token"
  },
  "http-basic": {},
  "gitlab-domains": ["gitlab的网址"]
}

```