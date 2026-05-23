<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Follow;
use App\Models\Like;
use App\Models\Pet;
use App\Models\PetRecord;
use App\Models\Post;
use App\Models\User;
use Faker\Generator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * 模拟数据填充器 — 覆盖用户、宠物、病历、帖子、评论、点赞、关注等核心业务数据。
 * 不清空数据表，仅追加数据。
 */
class SampleDataSeeder extends Seeder
{
    /** 物种列表（中文） */
    private const array SPECIES = ['狗', '猫', '兔子', '仓鼠', '龙猫', '乌龟', '鹦鹉', '其他'];

    /** 常见品种（按物种） */
    private const array BREEDS = [
        '狗' => ['金毛', '柯基', '泰迪', '哈士奇', '柴犬', '边牧', '萨摩耶', '比熊', '法斗', '博美'],
        '猫' => ['英短', '美短', '布偶', '暹罗', '橘猫', '三花', '缅因', '波斯', '折耳', '德文'],
        '兔子' => ['荷兰垂耳兔', '侏儒兔', '安哥拉兔', '狮子兔', '雷克斯兔'],
        '仓鼠' => ['金丝熊', '三线仓鼠', '一线仓鼠', '银狐仓鼠', '布丁仓鼠'],
        '龙猫' => ['标准灰', '银斑', '米色', '纯白'],
        '乌龟' => ['巴西龟', '草龟', '黄缘闭壳龟', '缅甸陆龟'],
        '鹦鹉' => ['虎皮鹦鹉', '玄凤鹦鹉', '牡丹鹦鹉', '金刚鹦鹉'],
        '其他' => ['中华田园', '混血'],
    ];

    /** 宠物名字候选 */
    private const array PET_NAMES = [
        '豆豆', '球球', '包子', '团子', '皮皮', '乐乐', '萌萌', '大宝',
        '小虎', '多多', '旺财', '来福', '小白', '小黑', '小花', '美美',
        '当当', '贝贝', '胖胖', '肉肉', '布丁', '奶茶', '饼干', '汤圆',
        '大壮', '小咪', '二狗', '元宝', '蛋黄', '雪糕', '可乐', '果冻',
    ];

    /** 医院名称候选 */
    private const array HOSPITALS = [
        '瑞鹏宠物医院', '芭比堂动物医院', '宠爱国际动物医院',
        '美联众合动物医院', '芭比堂宠物医院', '爱诺动物医院',
        '圣宠宠物医院', '宠颐生动物医院', '安安宠医', '联合宠物医院',
    ];

    /** 兽医姓氏候选 */
    private const array VET_SURNAMES = ['张', '李', '王', '刘', '陈', '杨', '赵', '黄', '周', '吴'];

    private const array VET_GIVENS = ['医生', '大夫', '医师', '主任'];

    /** 发布内容候选 */
    private const array POST_CONTENTS = [
        '今天带{pet}去公园散步，小家伙开心得不得了，跑了好几圈都不肯回家 🌿',
        '{pet}今天学会了新技能！坐下、握手都会了，太聪明了 🎉',
        '早上起来发现{pet}又在沙发上睡着了，这家伙真是越来越会享受了 😴',
        '{pet}的疫苗今天打了，很勇敢没有叫，奖励了零食 🩹',
        '{pet}的新造型！理发师手艺不错，是不是帅多了 💇',
        '分享一个{pet}日常：每次看到吃的就这样眼巴巴地看着，心都要化了 🥺',
        '{pet}今天第一次去宠物乐园，交到了好多新朋友 🐶🐱',
        '给{pet}买的新玩具到了，结果5分钟就玩坏了 😂',
        '今天下雨，{pet}只能在家玩了。看它无聊的样子好可爱 🌧️',
        '{pet}的生日到了！做了一顿大餐庆祝 🎂',
        '辟个谣：{pet}不是真的胖，只是毛茸茸的而已！',
        '早起遛狗打卡，{pet}精力旺盛，我已经走不动了 🏃',
        '{pet}绝育后恢复得不错，现在食量大增，要控制体重了 💪',
        '求问：{pet}这两天不怎么吃东西是怎么回事？有点担心 🤔',
        '{pet}和邻居家的猫成为好朋友了，每天隔着窗户打招呼 👋',
        '翻到{pet}小时候的照片，那时候好小一只，现在都长这么大了',
        '今天天气好，给{pet}洗了个澡，香喷喷的 🛁',
        '{pet}又拆家了！回家发现沙发背面多了个洞，血压上来了 😤',
        '给{pet}买了新窝，结果它还是喜欢睡我旁边 😅',
        '记录一下{pet}第一次游泳，狗刨式还挺标准的 🏊',
        '本周{pet}健康打卡：体重正常，精神状态好，排便正常 ✅',
        '{pet}的猫抓板终于到了！终于不抓我的沙发了 😌',
        '晒晒{pet}的零食柜，比我吃的都讲究',
        '学会了给{pet}剪指甲，虽然开始有点紧张但完成得还不错',
        '半夜{pet}突然开始叫，原来是窗外有只流浪猫 🐱',
    ];

    /** 帖子地点候选 */
    private const array LOCATIONS = [
        '北京·朝阳公园', '上海·世纪公园', '广州·白云山', '深圳·深圳湾公园',
        '杭州·西湖', '成都·人民公园', '南京·玄武湖', '武汉·东湖',
        '重庆·南山', '苏州·拙政园', null, null, null, null,
    ];

    /** 评论内容候选 */
    private const array COMMENT_CONTENTS = [
        '好可爱啊！', '太萌了', '这是什么品种？', '养得真好',
        '我家的也是这个品种', '哈哈太搞笑了', '好羡慕', '多大了呀？',
        '同款猫/狗！', '求问在哪里买的', '太好玩了', '好乖好乖',
        '哈哈哈哈笑死', '期待更多分享', '学到了', '收藏了',
        '多大了？', '好漂亮', '绝了', '啊啊啊好想摸',
    ];

    public function run(): void
    {
        $faker = fake('zh_CN');
        $password = Hash::make('password');

        // ─── 1. 创建用户 ───
        $users = new Collection;
        for ($i = 0; $i < 15; $i++) {
            $users->push(User::query()->create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'mobile' => $faker->unique()->numerify('1##########'),
                'status' => $faker->boolean(80) ? 1 : 0,
                'parent_id' => 0,
                'email_verified_at' => now(),
                'password' => $password,
                'remember_token' => $faker->regexify('[A-Za-z0-9]{10}'),
            ]));
        }

        $userIds = $users->pluck('id')->all();
        // 设置部分用户的上级关系
        foreach ($users as $user) {
            if ($faker->boolean(30)) {
                $candidates = array_values(array_filter(
                    $userIds,
                    static fn (int $id) => $id !== $user->id,
                ));
                if ($candidates !== []) {
                    $user->forceFill(['parent_id' => $faker->randomElement($candidates)])->save();
                }
            }
        }

        // ─── 2. 为每个用户创建宠物 ───
        $allPets = new Collection;

        foreach ($users as $index => $user) {
            $petCount = $faker->numberBetween(1, 5);
            $usedNames = [];

            for ($p = 0; $p < $petCount; $p++) {
                $species = $faker->randomElement(self::SPECIES);
                $breeds = self::BREEDS[$species] ?? self::BREEDS['其他'];
                $name = $faker->randomElement(array_diff(self::PET_NAMES, $usedNames));
                if ($name === null) {
                    $name = $faker->randomElement(self::PET_NAMES);
                }
                $usedNames[] = $name;

                $pet = Pet::query()->create([
                    'user_id' => $user->id,
                    'name' => $name,
                    'species' => $species,
                    'breed' => $faker->randomElement($breeds),
                    'gender' => $faker->randomElement(['male', 'female', 'unknown']),
                    'birthday' => $faker->dateTimeBetween('-10 years', '-2 months')->format('Y-m-d'),
                    'adoption_date' => $faker->dateTimeBetween('-5 years', '-1 week')->format('Y-m-d'),
                    'is_default' => $p === 0,
                    'status' => $faker->randomElement(['active', 'active', 'active', 'archived', 'deceased']),
                    'sort_order' => $p,
                ]);

                $allPets->push($pet);
            }
        }

        // ─── 3. 为每只宠物创建病历记录 ───
        $this->seedPetRecords($allPets, $faker);

        // ─── 4. 创建帖子 ───
        $allPosts = $this->seedPosts($users, $allPets, $faker);

        // ─── 5. 创建评论 ───
        $this->seedComments($allPosts, $users, $faker);

        // ─── 6. 创建点赞（帖子 + 评论） ───
        $this->seedLikes($allPosts, $users, $faker);

        // ─── 7. 创建关注关系 ───
        $this->seedFollows($users, $faker);

        $this->command?->info('✅ 模拟数据填充完成！');
        $this->command?->info("   用户：{$users->count()} | 宠物：{$allPets->count()} | 帖子：{$allPosts->count()}");
    }

    private function seedPetRecords(Collection $pets, Generator $faker): void
    {
        $recordTypes = ['vaccine', 'checkup', 'illness', 'medication', 'surgery', 'grooming', 'other'];
        $vaccineNames = ['狂犬疫苗', '犬八联疫苗', '猫三联疫苗', '五联疫苗', '犬四联疫苗'];
        $illnessNames = ['感冒', '腹泻', '皮肤病', '耳螨', '结膜炎', '食欲不振', '呕吐', '外伤'];
        $checkupNames = ['年度体检', '血常规检查', '生化检查', 'B超检查', '牙科检查'];
        $groomingNames = ['洗澡美容', '剪指甲', '挤肛门腺', '毛发护理', 'SPA'];

        foreach ($pets as $pet) {
            $recordCount = $faker->numberBetween(2, 10);

            for ($r = 0; $r < $recordCount; $r++) {
                $type = $faker->randomElement($recordTypes);
                $visitDate = $faker->dateTimeBetween($pet->adoption_date ?? '-2 years', 'now');

                $title = match ($type) {
                    'vaccine' => $faker->randomElement($vaccineNames),
                    'checkup' => $faker->randomElement($checkupNames),
                    'illness' => $faker->randomElement($illnessNames),
                    'grooming' => $faker->randomElement($groomingNames),
                    'medication' => '驱虫用药',
                    'surgery' => $faker->randomElement(['绝育手术', '洗牙手术', '肿瘤切除']),
                    'other' => $faker->sentence(3),
                };

                PetRecord::query()->create([
                    'pet_id' => $pet->id,
                    'type' => $type,
                    'title' => $title,
                    'visit_date' => $visitDate->format('Y-m-d'),
                    'next_visit_date' => $faker->optional(0.4)->dateTimeBetween('+1 month', '+1 year')?->format('Y-m-d'),
                    'hospital_name' => $faker->randomElement(self::HOSPITALS),
                    'vet_name' => $faker->randomElement(self::VET_SURNAMES).$faker->randomElement(self::VET_GIVENS),
                    'hospital_phone' => $faker->phoneNumber(),
                    'weight' => $faker->randomFloat(2, 0.5, 40),
                    'temperature' => $faker->randomFloat(1, 37, 39.5),
                    'symptoms' => $type === 'illness' ? $faker->paragraph() : null,
                    'diagnosis' => $type === 'illness' ? $faker->paragraph() : null,
                    'treatment' => in_array($type, ['illness', 'surgery'], true) ? $faker->paragraph() : null,
                    'prescription' => in_array($type, ['illness', 'medication'], true) ? $faker->paragraph() : null,
                    'notes' => $faker->optional(0.5)->paragraph(),
                    'cost' => $faker->randomFloat(2, 50, 5000),
                ]);
            }
        }
    }

    private function seedPosts(Collection $users, Collection $pets, Generator $faker): Collection
    {
        $posts = new Collection;

        foreach ($users as $user) {
            $userPets = $pets->where('user_id', $user->id)->values();
            $postCount = $faker->numberBetween(2, 8);

            for ($p = 0; $p < $postCount; $p++) {
                $pet = $userPets->isNotEmpty() && $faker->boolean(80)
                    ? $userPets->random()
                    : null;

                $content = $faker->randomElement(self::POST_CONTENTS);
                if ($pet) {
                    $content = str_replace('{pet}', $pet->name, $content);
                } else {
                    $content = str_replace('{pet}', '我家毛孩子', $content);
                }

                $publishedAt = $faker->dateTimeBetween('-6 months', 'now');

                $post = Post::query()->create([
                    'user_id' => $user->id,
                    'pet_id' => $pet?->id,
                    'content' => $content,
                    'location' => $faker->randomElement(self::LOCATIONS),
                    'visibility' => $faker->randomElement(['public', 'public', 'public', 'followers', 'private']),
                    'like_count' => 0,
                    'comment_count' => 0,
                    'view_count' => $faker->numberBetween(50, 5000),
                    'share_count' => $faker->numberBetween(0, 50),
                    'is_pinned' => $p === 0 && $faker->boolean(20),
                    'allow_comment' => $faker->boolean(90),
                    'published_at' => $publishedAt,
                ]);

                $posts->push($post);
            }
        }

        return $posts;
    }

    private function seedComments(Collection $posts, Collection $users, Generator $faker): Collection
    {
        $comments = new Collection;
        $userIds = $users->pluck('id')->all();

        foreach ($posts as $post) {
            if (! $post->allow_comment) {
                continue;
            }

            $commentCount = $faker->numberBetween(0, 8);

            for ($c = 0; $c < $commentCount; $c++) {
                $comment = Comment::query()->create([
                    'user_id' => $this->randomOtherUserId($post->user_id, $userIds, $faker),
                    'post_id' => $post->id,
                    'parent_id' => null,
                    'content' => $faker->randomElement(self::COMMENT_CONTENTS),
                    'like_count' => 0,
                ]);
                $comments->push($comment);

                // 为部分评论生成子回复
                if ($faker->boolean(25)) {
                    $replyCount = $faker->numberBetween(1, 2);
                    for ($r = 0; $r < $replyCount; $r++) {
                        $reply = Comment::query()->create([
                            'user_id' => $this->randomOtherUserId($post->user_id, $userIds, $faker),
                            'post_id' => $post->id,
                            'parent_id' => $comment->id,
                            'content' => $faker->randomElement(self::COMMENT_CONTENTS),
                            'like_count' => 0,
                        ]);
                        $comments->push($reply);
                    }
                }
            }

            // 更新帖子的评论计数
            $post->update(['comment_count' => $post->comments()->count()]);
        }

        return $comments;
    }

    private function seedLikes(Collection $posts, Collection $users, Generator $faker): void
    {
        $userIds = $users->pluck('id')->all();

        // 点赞帖子
        foreach ($posts as $post) {
            if ($post->visibility !== 'public' && $post->visibility !== 'followers') {
                continue;
            }

            $likeCount = $faker->numberBetween(0, 15);

            // 随机选出不重复的用户来点赞
            $potentialLikers = array_values(array_filter(
                $userIds,
                static fn (int $id) => $id !== $post->user_id,
            ));
            shuffle($potentialLikers);
            $likers = array_slice($potentialLikers, 0, min($likeCount, count($potentialLikers)));

            foreach ($likers as $likerId) {
                Like::query()->firstOrCreate([
                    'user_id' => $likerId,
                    'likeable_type' => Post::class,
                    'likeable_id' => $post->id,
                ], ['created_at' => now()]);
            }

            if ($likers !== []) {
                $post->update(['like_count' => count($likers)]);
            }
        }

        // 点赞评论
        $comments = Comment::query()->whereIn('post_id', $posts->pluck('id'))->get();
        foreach ($comments as $comment) {
            $likeCount = $faker->numberBetween(0, 5);

            $potentialLikers = array_values(array_filter(
                $userIds,
                static fn (int $id) => $id !== $comment->user_id,
            ));
            shuffle($potentialLikers);
            $likers = array_slice($potentialLikers, 0, min($likeCount, count($potentialLikers)));

            foreach ($likers as $likerId) {
                Like::query()->firstOrCreate([
                    'user_id' => $likerId,
                    'likeable_type' => Comment::class,
                    'likeable_id' => $comment->id,
                ], ['created_at' => now()]);
            }

            if ($likers !== []) {
                $comment->update(['like_count' => count($likers)]);
            }
        }
    }

    private function seedFollows(Collection $users, Generator $faker): void
    {
        $userIds = $users->pluck('id')->all();

        foreach ($users as $user) {
            $followCount = $faker->numberBetween(0, 10);

            $potentialTargets = array_values(array_filter(
                $userIds,
                static fn (int $id) => $id !== $user->id,
            ));
            shuffle($potentialTargets);
            $targets = array_slice($potentialTargets, 0, min($followCount, count($potentialTargets)));

            foreach ($targets as $targetId) {
                Follow::query()->firstOrCreate([
                    'follower_id' => $user->id,
                    'following_id' => $targetId,
                ], ['created_at' => now()]);
            }
        }
    }

    /**
     * 获取一个与指定用户不同的随机用户 ID。
     */
    private function randomOtherUserId(int $excludeId, array $allIds, Generator $faker): int
    {
        $candidates = array_values(array_filter($allIds, static fn (int $id) => $id !== $excludeId));

        return $candidates !== []
            ? $faker->randomElement($candidates)
            : $excludeId;
    }
}
