# 题目描述

I created this new guessing game for Android. To deter cheaters, I made sure to include advanced military grade encryption.

Please don't cheat, Thanks!

# 解决方案

直接jadx走一波，就可以看到[GotAnyGames.apk](file/GotAnyGames.apk)对应的源码，发现解析sav文件的为com.example.gotanygames.LoadGameActivity类，其中关键部分的源码如下：

```java
package com.example.gotanygames;

import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.TextView;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import java.io.FileInputStream;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;
import javax.crypto.Cipher;
import javax.crypto.Mac;
import javax.crypto.spec.IvParameterSpec;
import javax.crypto.spec.SecretKeySpec;
import kotlin.Metadata;
import kotlin.Unit;
import kotlin.collections.ArraysKt;
import kotlin.collections.CollectionsKt;
import kotlin.io.ByteStreamsKt;
import kotlin.io.CloseableKt;
import kotlin.jvm.internal.Intrinsics;
import kotlin.jvm.internal.Ref;
import kotlin.ranges.RangesKt;
import kotlin.text.Charsets;
import kotlin.text.StringsKt;

/* compiled from: LoadGameActivity.kt */
@Metadata(d1 = {"\u0000(\n\u0002\u0018\u0002\n\u0002\u0018\u0002\n\u0002\b\u0002\n\u0002\u0010\u0012\n\u0002\b\u0003\n\u0002\u0010\u000e\n\u0002\b\u0002\n\u0002\u0010\u0002\n\u0000\n\u0002\u0018\u0002\n\u0000\u0018\u00002\u00020\u0001B\u0005¢\u0006\u0002\u0010\u0002J\u0012\u0010\u0007\u001a\u0004\u0018\u00010\b2\u0006\u0010\t\u001a\u00020\u0004H\u0002J\u0012\u0010\n\u001a\u00020\u000b2\b\u0010\f\u001a\u0004\u0018\u00010\rH\u0014R\u0011\u0010\u0003\u001a\u00020\u0004¢\u0006\b\n\u0000\u001a\u0004\b\u0005\u0010\u0006¨\u0006\u000e"}, d2 = {"Lcom/example/gotanygames/LoadGameActivity;", "Landroidx/appcompat/app/AppCompatActivity;", "()V", "k", "", "getK", "()[B", "decrypt", "", "data", "onCreate", "", "savedInstanceState", "Landroid/os/Bundle;", "app_release"}, k = 1, mv = {1, 7, 1}, xi = 48)
/* loaded from: classes.dex */
public final class LoadGameActivity extends AppCompatActivity {
    private final byte[] k;

    public LoadGameActivity() {
        List<Number> listOf = CollectionsKt.listOf((Object[]) new Integer[]{238, 236, 133, 123, 132, 215, 41, 111, 93, 8, 227, 45, 179, 170, 235, 139, 150, 187, 160, 231, 187, 46, 155, 206, 207, 143, 107, 226, 131, 54, 202, 248});
        ArrayList arrayList = new ArrayList(CollectionsKt.collectionSizeOrDefault(listOf, 10));
        for (Number number : listOf) {
            arrayList.add(Byte.valueOf((byte) number.intValue()));
        }
        this.k = CollectionsKt.toByteArray(arrayList);
    }

    public final byte[] getK() {
        return this.k;
    }

    /* JADX INFO: Access modifiers changed from: protected */
    @Override // androidx.fragment.app.FragmentActivity, androidx.activity.ComponentActivity, androidx.core.app.ComponentActivity, android.app.Activity
    public void onCreate(Bundle bundle) {
        super.onCreate(bundle);
        setContentView(R.layout.activity_load_game);
        final Ref.IntRef intRef = new Ref.IntRef();
        final TextView textView = (TextView) findViewById(R.id.savedGamesView);
        String str = getApplicationContext().fileList()[intRef.element];
        Intrinsics.checkNotNullExpressionValue(str, "applicationContext.fileList()[idx]");
        textView.setText(StringsKt.removeSuffix(str, (CharSequence) ".sav"));
        ((Button) findViewById(R.id.deleteButton)).setOnClickListener(new View.OnClickListener() { // from class: com.example.gotanygames.LoadGameActivity$$ExternalSyntheticLambda0
            @Override // android.view.View.OnClickListener
            public final void onClick(View view) {
                LoadGameActivity.onCreate$lambda$1(LoadGameActivity.this, textView, view);
            }
        });
        ((Button) findViewById(R.id.nextButton)).setOnClickListener(new View.OnClickListener() { // from class: com.example.gotanygames.LoadGameActivity$$ExternalSyntheticLambda1
            @Override // android.view.View.OnClickListener
            public final void onClick(View view) {
                LoadGameActivity.onCreate$lambda$2(Ref.IntRef.this, this, textView, view);
            }
        });
        ((Button) findViewById(R.id.prevButton)).setOnClickListener(new View.OnClickListener() { // from class: com.example.gotanygames.LoadGameActivity$$ExternalSyntheticLambda2
            @Override // android.view.View.OnClickListener
            public final void onClick(View view) {
                LoadGameActivity.onCreate$lambda$3(Ref.IntRef.this, this, textView, view);
            }
        });
        ((Button) findViewById(R.id.loadButton)).setOnClickListener(new View.OnClickListener() { // from class: com.example.gotanygames.LoadGameActivity$$ExternalSyntheticLambda3
            @Override // android.view.View.OnClickListener
            public final void onClick(View view) {
                LoadGameActivity.onCreate$lambda$7(textView, this, view);
            }
        });
    }

    /* JADX INFO: Access modifiers changed from: private */
    public static final void onCreate$lambda$1(LoadGameActivity this$0, TextView textView, View view) {
        Intrinsics.checkNotNullParameter(this$0, "this$0");
        this$0.getApplicationContext().deleteFile(((Object) textView.getText()) + ".sav");
        String[] fList = this$0.getApplicationContext().fileList();
        Intrinsics.checkNotNullExpressionValue(fList, "fList");
        if (!(fList.length == 0)) {
            String str = fList[0];
            Intrinsics.checkNotNullExpressionValue(str, "fList[0]");
            textView.setText(StringsKt.removeSuffix(str, (CharSequence) ".sav"));
            return;
        }
        Toast.makeText(this$0.getApplicationContext(), "No saved games available.", 0).show();
        this$0.finish();
    }

    /* JADX INFO: Access modifiers changed from: private */
    public static final void onCreate$lambda$2(Ref.IntRef idx, LoadGameActivity this$0, TextView textView, View view) {
        Intrinsics.checkNotNullParameter(idx, "$idx");
        Intrinsics.checkNotNullParameter(this$0, "this$0");
        idx.element = (idx.element + 1) % this$0.getApplicationContext().fileList().length;
        String str = this$0.getApplicationContext().fileList()[idx.element];
        Intrinsics.checkNotNullExpressionValue(str, "applicationContext.fileList()[idx]");
        textView.setText(StringsKt.removeSuffix(str, (CharSequence) ".sav"));
    }

    /* JADX INFO: Access modifiers changed from: private */
    public static final void onCreate$lambda$3(Ref.IntRef idx, LoadGameActivity this$0, TextView textView, View view) {
        Intrinsics.checkNotNullParameter(idx, "$idx");
        Intrinsics.checkNotNullParameter(this$0, "this$0");
        idx.element = (idx.element + (-1) < 0 ? this$0.getApplicationContext().fileList().length : idx.element) - 1;
        String str = this$0.getApplicationContext().fileList()[idx.element];
        Intrinsics.checkNotNullExpressionValue(str, "applicationContext.fileList()[idx]");
        textView.setText(StringsKt.removeSuffix(str, (CharSequence) ".sav"));
    }

    /* JADX INFO: Access modifiers changed from: private */
    /* JADX WARN: Finally extract failed */
    public static final void onCreate$lambda$7(TextView textView, LoadGameActivity this$0, View view) {
        Intrinsics.checkNotNullParameter(this$0, "this$0");
        String obj = textView.getText().toString();
        FileInputStream openFileInput = this$0.getApplicationContext().openFileInput(obj + ".sav");
        try {
            FileInputStream it = openFileInput;
            Intrinsics.checkNotNullExpressionValue(it, "it");
            byte[] readBytes = ByteStreamsKt.readBytes(it);
            Unit unit = Unit.INSTANCE;
            Unit unit2 = null;
            CloseableKt.closeFinally(openFileInput, null);
            String decrypt = this$0.decrypt(readBytes);
            if (decrypt != null) {
                Intent intent = new Intent(this$0, PlayGame.class);
                intent.putExtra("gameState", decrypt);
                intent.putExtra("saveName", obj);
                this$0.startActivity(intent);
                unit2 = Unit.INSTANCE;
            }
            if (unit2 != null) {
                return;
            }
            Toast.makeText(this$0.getApplicationContext(), "Save file is corrupted.", 0).show();
        } catch (Throwable th) {
            try {
                throw th;
            } catch (Throwable th2) {
                CloseableKt.closeFinally(openFileInput, th);
                throw th2;
            }
        }
    }

    private final String decrypt(byte[] bArr) {
        if (bArr.length < 48) {
            return null;
        }
        Mac mac = Mac.getInstance("HmacSHA256");
        mac.init(new SecretKeySpec(this.k, 0, 32, "HmacSHA256"));
        if (!Arrays.equals(mac.doFinal(CollectionsKt.toByteArray(ArraysKt.slice(bArr, RangesKt.until(0, bArr.length - 32)))), CollectionsKt.toByteArray(ArraysKt.slice(bArr, RangesKt.until(bArr.length - 32, bArr.length))))) {
            return null;
        }
        byte[] byteArray = CollectionsKt.toByteArray(ArraysKt.slice(bArr, RangesKt.until(0, 16)));
        byte[] byteArray2 = CollectionsKt.toByteArray(ArraysKt.slice(bArr, RangesKt.until(16, bArr.length - 32)));
        Cipher cipher = Cipher.getInstance("AES/CBC/PKCS7Padding");
        cipher.init(2, new SecretKeySpec(this.k, 0, 32, "AES"), new IvParameterSpec(byteArray));
        byte[] decryptedData = cipher.doFinal(byteArray2);
        if (decryptedData[0] == 123) {
            Intrinsics.checkNotNullExpressionValue(decryptedData, "decryptedData");
            if (ArraysKt.last(decryptedData) == 125) {
                return new String(decryptedData, Charsets.UTF_8);
            }
        }
        return null;
    }
}
```

可以从源码中知道key，以及[sav文件](file/SaveFile.sav)的前16个字节为IV，最后32个字节为校验和，中间部分为需要解密的数据，使用AES/CBC进行解密，解密结果如下：

```json
{"points": 10000000, "flag": "flag{38002abd05c651c83e1d4c0177a8eaca}\n"}
```
