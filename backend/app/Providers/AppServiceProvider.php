<?php

namespace App\Providers;

use App\Domain\AI\AIService;
use App\Domain\AI\Contracts\AIServiceInterface;
use App\Domain\Audit\AuditObserver;
use App\Domain\Audit\AuditRepository;
use App\Domain\Audit\AuditService;
use App\Domain\Audit\Contracts\AuditRepositoryInterface;
use App\Domain\Audit\Contracts\AuditServiceInterface;
use App\Domain\Category\CategoryObserver;
use App\Domain\Category\CategoryRepository;
use App\Domain\Category\CategoryService;
use App\Domain\Category\Contracts\CategoryRepositoryInterface;
use App\Domain\Category\Contracts\CategoryServiceInterface;
use App\Domain\Creator\Contracts\CreatorRepositoryInterface;
use App\Domain\Creator\Contracts\CreatorServiceInterface;
use App\Domain\Creator\CreatorObserver;
use App\Domain\Creator\CreatorRepository;
use App\Domain\Creator\CreatorService;
use App\Domain\Creator\Events\CreatorCreated;
use App\Domain\Creator\Events\CreatorDeleted;
use App\Domain\Creator\Events\CreatorUpdated;
use App\Domain\Dashboard\Contracts\DashboardServiceInterface;
use App\Domain\Dashboard\DashboardService;
use App\Domain\Graph\Contracts\RegionGraphRepositoryInterface;
use App\Domain\Graph\Contracts\RegionGraphServiceInterface;
use App\Domain\Graph\RegionGraphRepository;
use App\Domain\Graph\RegionGraphService;
use App\Domain\Knowledge\Contracts\KnowledgeRepositoryInterface;
use App\Domain\Knowledge\KnowledgeRepository;
use App\Domain\Media\Contracts\MediaRepositoryInterface;
use App\Domain\Media\Contracts\MediaServiceInterface;
use App\Domain\Media\Events\MediaCreated;
use App\Domain\Media\Events\MediaDeleted;
use App\Domain\Media\MediaObserver;
use App\Domain\Media\MediaRepository;
use App\Domain\Media\MediaService;
use App\Domain\Media\MediaUploadService;
use App\Domain\Product\Contracts\ProductRepositoryInterface;
use App\Domain\Product\Contracts\ProductServiceInterface;
use App\Domain\Product\Events\ProductCreated;
use App\Domain\Product\Events\ProductDeleted;
use App\Domain\Product\Events\ProductUpdated;
use App\Domain\Product\ProductObserver;
use App\Domain\Product\ProductRepository;
use App\Domain\Product\ProductService;
use App\Domain\Recommendation\Contracts\RecommendationRepositoryInterface;
use App\Domain\Recommendation\Contracts\RecommendationServiceInterface;
use App\Domain\Recommendation\RecommendationRepository;
use App\Domain\Recommendation\RecommendationService;
use App\Domain\Recommendation\Strategies\ContentSimilarityStrategy;
use App\Domain\Recommendation\Strategies\RecommendationStrategyInterface;
use App\Domain\Region\Contracts\RegionRepositoryInterface;
use App\Domain\Region\Contracts\RegionServiceInterface;
use App\Domain\Region\Events\RegionCreated;
use App\Domain\Region\Events\RegionDeleted;
use App\Domain\Region\Events\RegionUpdated;
use App\Domain\Region\RegionObserver;
use App\Domain\Region\RegionRepository;
use App\Domain\Region\RegionService;
use App\Domain\Search\Contracts\SearchServiceInterface;
use App\Domain\Search\SearchService;
use App\Domain\Story\Contracts\StoryRepositoryInterface;
use App\Domain\Story\Contracts\StoryServiceInterface;
use App\Domain\Story\Events\StoryCreated;
use App\Domain\Story\Events\StoryDeleted;
use App\Domain\Story\Events\StoryUpdated;
use App\Domain\Story\StoryObserver;
use App\Domain\Story\StoryRepository;
use App\Domain\Story\StoryService;
use App\Domain\Tag\Contracts\TagRepositoryInterface;
use App\Domain\Tag\Contracts\TagServiceInterface;
use App\Domain\Tag\TagObserver;
use App\Domain\Tag\TagRepository;
use App\Domain\Tag\TagService;
use App\Domain\User\Contracts\UserRepositoryInterface;
use App\Domain\User\Contracts\UserServiceInterface;
use App\Domain\User\Events\UserRegistered;
use App\Domain\User\UserObserver;
use App\Domain\User\UserRepository;
use App\Domain\User\UserService;
use App\Listeners\HandleCreatorCreated;
use App\Listeners\HandleCreatorDeleted;
use App\Listeners\HandleCreatorUpdated;
use App\Listeners\HandleMediaCreated;
use App\Listeners\HandleMediaDeleted;
use App\Listeners\HandleProductCreated;
use App\Listeners\HandleProductDeleted;
use App\Listeners\HandleProductUpdated;
use App\Listeners\HandleRegionCreated;
use App\Listeners\HandleRegionDeleted;
use App\Listeners\HandleRegionUpdated;
use App\Listeners\HandleStoryCreated;
use App\Listeners\HandleStoryDeleted;
use App\Listeners\HandleStoryUpdated;
use App\Listeners\HandleUserRegistered;
use App\Models\AuditLog;
use App\Models\Category;
use App\Models\Creator;
use App\Models\Media;
use App\Models\Product;
use App\Models\Region;
use App\Models\Story;
use App\Models\Tag;
use App\Models\User;
use App\Policies\AuditPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\CreatorPolicy;
use App\Policies\MediaPolicy;
use App\Policies\ProductPolicy;
use App\Policies\RegionPolicy;
use App\Policies\StoryPolicy;
use App\Policies\TagPolicy;
use App\Policies\UserPolicy;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Region Domain
        $this->app->bind(RegionRepositoryInterface::class, RegionRepository::class);
        $this->app->bind(RegionServiceInterface::class, RegionService::class);

        // Product Domain
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
        $this->app->bind(ProductServiceInterface::class, ProductService::class);

        // Story Domain
        $this->app->bind(StoryRepositoryInterface::class, StoryRepository::class);
        $this->app->bind(StoryServiceInterface::class, StoryService::class);

        // Creator Domain
        $this->app->bind(CreatorRepositoryInterface::class, CreatorRepository::class);
        $this->app->bind(CreatorServiceInterface::class, CreatorService::class);

        // Graph Domain
        $this->app->bind(RegionGraphRepositoryInterface::class, RegionGraphRepository::class);
        $this->app->bind(RegionGraphServiceInterface::class, RegionGraphService::class);

        // Recommendation Domain
        $this->app->bind(RecommendationRepositoryInterface::class, RecommendationRepository::class);
        $this->app->bind(RecommendationServiceInterface::class, RecommendationService::class);
        $this->app->bind(RecommendationStrategyInterface::class, ContentSimilarityStrategy::class);

        // Search Domain
        $this->app->bind(SearchServiceInterface::class, SearchService::class);

        // AI Domain
        $this->app->bind(AIServiceInterface::class, AIService::class);

        // Knowledge Domain
        $this->app->bind(KnowledgeRepositoryInterface::class, KnowledgeRepository::class);

        // Category Domain
        $this->app->bind(CategoryRepositoryInterface::class, CategoryRepository::class);
        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);

        // Media Domain
        $this->app->bind(MediaRepositoryInterface::class, MediaRepository::class);
        $this->app->bind(MediaServiceInterface::class, MediaService::class);

        // Audit Domain
        $this->app->bind(AuditRepositoryInterface::class, AuditRepository::class);
        $this->app->bind(AuditServiceInterface::class, AuditService::class);

        // Dashboard Domain
        $this->app->bind(DashboardServiceInterface::class, DashboardService::class);

        // Tag Domain
        $this->app->bind(TagRepositoryInterface::class, TagRepository::class);
        $this->app->bind(TagServiceInterface::class, TagService::class);

        // User Domain
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(UserServiceInterface::class, UserService::class);

        // Media Upload Service (not interface-bound — concrete class injected directly)
        $this->app->bind(MediaUploadService::class, function ($app) {
            return new MediaUploadService(
                $app->make(MediaRepository::class),
                config('filesystems.default', 'public')
            );
        });
    }

    public function boot(): void
    {
        // Register Observers
        Region::observe(RegionObserver::class);
        Product::observe(ProductObserver::class);
        Story::observe(StoryObserver::class);
        Creator::observe(CreatorObserver::class);
        Category::observe(CategoryObserver::class);
        Media::observe(MediaObserver::class);
        AuditLog::observe(AuditObserver::class);
        User::observe(UserObserver::class);
        Tag::observe(TagObserver::class);

        // Register Policies
        Gate::policy(Region::class, RegionPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Story::class, StoryPolicy::class);
        Gate::policy(Creator::class, CreatorPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Media::class, MediaPolicy::class);
        Gate::policy(AuditLog::class, AuditPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);

        // Register Event Listeners
        Event::listen(UserRegistered::class, HandleUserRegistered::class);

        Event::listen(RegionCreated::class, HandleRegionCreated::class);
        Event::listen(RegionUpdated::class, HandleRegionUpdated::class);
        Event::listen(RegionDeleted::class, HandleRegionDeleted::class);

        Event::listen(ProductCreated::class, HandleProductCreated::class);
        Event::listen(ProductUpdated::class, HandleProductUpdated::class);
        Event::listen(ProductDeleted::class, HandleProductDeleted::class);

        Event::listen(StoryCreated::class, HandleStoryCreated::class);
        Event::listen(StoryUpdated::class, HandleStoryUpdated::class);
        Event::listen(StoryDeleted::class, HandleStoryDeleted::class);

        Event::listen(CreatorCreated::class, HandleCreatorCreated::class);
        Event::listen(CreatorUpdated::class, HandleCreatorUpdated::class);
        Event::listen(CreatorDeleted::class, HandleCreatorDeleted::class);

        Event::listen(MediaCreated::class, HandleMediaCreated::class);
        Event::listen(MediaDeleted::class, HandleMediaDeleted::class);
    }
}
